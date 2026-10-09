<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CourseFailed;
use App\Mail\CoursePassed;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamCourse;
use App\Models\ExamQuestion;
use App\Models\ExamQuestionOption;
use App\Models\UserCourse;
use App\Models\UserCourseExam;
use App\Models\UserCourseExamResult;
use App\Services\CourseAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ExamController extends Controller
{
    public function __construct(private readonly CourseAccessService $access)
    {
    }

    public function show(Request $request, Course $course): JsonResponse
    {
        $userCourse = $this->access->resolve($request->user(), $course);
        $exam = $this->findExam($course);

        $this->ensureAllChaptersReady($course, $userCourse);

        $this->closeAttemptIfTimeIsUp($userCourse, $exam);
        $attempt = $this->attemptInProgress($userCourse, $exam);

        $this->ensureAttemptsLeft($userCourse);

        $questions = ExamQuestion::where('exam_id', $exam->id)->orderBy('id')->with('examquestionoptions')->get();

        return $this->ok([
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'description' => $exam->description,
                'duration_seconds' => $exam->duration ? ((int) $exam->duration * 60) : 7200,
            ],
            'pass_threshold' => CourseAccessService::PASS_THRESHOLD,
            'attempts_used' => (int) $userCourse->intentos,
            'attempts_left' => max(0, CourseAccessService::MAX_EXAM_ATTEMPTS - (int) $userCourse->intentos),
            'max_attempts' => CourseAccessService::MAX_EXAM_ATTEMPTS,
            'approved' => (bool) $userCourse->aprobado,
            'in_progress' => (bool) ($attempt && (int) $attempt->complete !== 1),
            'remaining_time' => $this->remainingTime($attempt, $exam),
            'total_questions' => $questions->count(),
            'questions' => $questions->map(fn (ExamQuestion $question) => [
                'id' => $question->id,
                'question' => $question->question,
                'options' => $question->examquestionoptions->map(fn (ExamQuestionOption $option) => [
                    'id' => $option->id,
                    'option' => $option->opcion,
                ])->values(),
            ])->values(),
        ]);
    }

    /**
     * El cronómetro arranca aquí, no en el navegador: el alumno acepta las instrucciones y
     * el servidor anota la hora de inicio. Así el intento vence aunque cierre la ventana
     * (#1824), y recargar la página ya no regala dos horas nuevas.
     */
    public function start(Request $request, Course $course): JsonResponse
    {
        $userCourse = $this->access->resolve($request->user(), $course);
        $exam = $this->findExam($course);

        $this->ensureAllChaptersReady($course, $userCourse);

        if ((int) $userCourse->aprobado === 1) {
            return response()->json(['success' => false, 'message' => 'You already have the course approved.'], 422);
        }

        $this->closeAttemptIfTimeIsUp($userCourse, $exam);

        $this->ensureAttemptsLeft($userCourse);

        $attempt = UserCourseExam::where('user_course_id', $userCourse->id)->where('exam_id', $exam->id)->first();

        if (! $attempt) {
            $attempt = UserCourseExam::create([
                'user_course_id' => $userCourse->id,
                'exam_id' => $exam->id,
                'intentos' => 0,
                'complete' => 0,
                'resultado' => 0,
                'tiempo' => $this->durationLabel($exam),
                'started_at' => now(),
            ]);
        } elseif ((int) $attempt->complete === 1) {
            // Intento anterior ya entregado: éste empieza de cero, con su hora.
            $attempt->update(['complete' => 0, 'evento' => null, 'tiempo' => $this->durationLabel($exam), 'started_at' => now()]);
        } elseif (! $attempt->started_at) {
            // Intento abierto por una versión anterior, sin hora de inicio.
            $attempt->update(['started_at' => now()]);
        }

        return $this->ok([
            'remaining_time' => $this->remainingTime($attempt->refresh(), $exam),
        ] + $this->attemptsPayload($userCourse));
    }

    public function submit(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'answers' => ['present', 'array'],
            'answers.*.exam_question_id' => ['required', 'integer'],
            'answers.*.exam_question_option_id' => ['required', 'integer'],
            'remaining_time' => ['nullable', 'string', 'max:20'],
            'expired' => ['nullable', 'boolean'],
        ]);

        $userCourse = $this->access->resolve($request->user(), $course);
        $exam = $this->findExam($course);
        $this->ensureAllChaptersReady($course, $userCourse);

        if ((int) $userCourse->aprobado === 1) {
            return response()->json(['success' => false, 'message' => 'You already have the course approved.'], 422);
        }

        // El plazo manda sobre lo que diga el navegador: una entrega fuera de hora no se
        // corrige, se cierra como intento agotado.
        if ($this->closeAttemptIfTimeIsUp($userCourse, $exam)) {
            return $this->ok([
                'completed' => false,
                'expired' => true,
                'message' => 'Time is up.',
            ] + $this->attemptsPayload($userCourse->refresh()));
        }

        $this->ensureAttemptsLeft($userCourse);

        $attempt = $this->attemptInProgress($userCourse, $exam) ?: UserCourseExam::firstOrCreate(
            ['user_course_id' => $userCourse->id, 'exam_id' => $exam->id],
            ['intentos' => 0, 'complete' => 0, 'resultado' => 0, 'tiempo' => '00:00:00', 'started_at' => now()],
        );

        $attemptNumber = (int) $attempt->intentos + 1;

        // Se acabó el tiempo: cuenta como intento fallido, igual que entregar en blanco.
        if ($validated['expired'] ?? false) {
            $attempt->update(['intentos' => $attemptNumber, 'tiempo' => $validated['remaining_time'] ?? '00:00:00', 'resultado' => 0, 'complete' => 1, 'evento' => 'excedio', 'started_at' => null]);
            $userCourse->update(['aprobado' => 0, 'intentos' => (int) $userCourse->intentos + 1]);

            return $this->ok([
                'completed' => false,
                'expired' => true,
                'message' => 'Time is up.',
            ] + $this->attemptsPayload($userCourse->refresh()));
        }

        // La nota se calcula sobre TODAS las preguntas del examen, no sobre las respuestas
        // enviadas: una pregunta sin responder cuenta como fallada. Si no, bastaba con
        // mandar una sola respuesta correcta para sacar el 100 %.
        $questions = ExamQuestion::where('exam_id', $exam->id)->pluck('id');
        $answers = collect($validated['answers'])->keyBy('exam_question_id');

        $totalCorrect = 0;
        $review = [];
        $timeSpent = $this->timeSpent($attempt, $exam);

        DB::transaction(function () use ($questions, $answers, $attempt, $attemptNumber, &$totalCorrect, &$review): void {
            foreach ($questions as $questionId) {
                $answer = $answers->get($questionId);
                $option = $answer
                    ? ExamQuestionOption::where('id', $answer['exam_question_option_id'])->where('exam_question_id', $questionId)->first()
                    : null;
                $result = $option && (int) $option->resultado === 1 ? 1 : 0;
                $totalCorrect += $result;

                // Sin opción válida no hay fila (la columna no admite nulos): la pregunta
                // cuenta como fallada en la nota, que se calcula sobre el total del examen.
                $question = ExamQuestion::with('examquestionoptions')->find($questionId);

                // Para la pantalla "View questions": qué contestó y cuál era la correcta.
                $review[] = [
                    'question' => $question?->question,
                    'your_answer' => $option?->opcion,
                    'correct_answer' => $question?->examquestionoptions->firstWhere('resultado', 1)?->opcion,
                    'correct' => (bool) $result,
                ];

                if ($option) {
                    UserCourseExamResult::create([
                        'user_course_exam_id' => $attempt->id,
                        'attempt_number' => $attemptNumber,
                        'exam_question_id' => $questionId,
                        'exam_question_option_id' => $option->id,
                        'result' => $result,
                    ]);
                }
            }

            $attempt->update(['intentos' => $attemptNumber, 'complete' => 1, 'tiempo' => $validated['remaining_time'] ?? $attempt->tiempo, 'started_at' => null]);
        });

        $totalQuestions = $questions->count();
        $percentage = $totalQuestions > 0 ? round($totalCorrect * 100 / $totalQuestions, 2) : 0.0;
        $passed = $totalQuestions > 0 && $percentage >= CourseAccessService::PASS_THRESHOLD;

        $attempt->update(['resultado' => $percentage]);

        if ($passed) {
            $userCourse->update(['aprobado' => 1, 'finalizado' => 1]);
            $this->sendMail($userCourse->user?->email, new CoursePassed($userCourse, $course));
        } else {
            $userCourse->update(['aprobado' => 0, 'intentos' => (int) $userCourse->intentos + 1]);

            // Como en producción: el aviso de no aprobado sale al agotar los intentos,
            // no en cada suspenso (#1831).
            if ((int) $userCourse->refresh()->intentos >= CourseAccessService::MAX_EXAM_ATTEMPTS) {
                $this->sendMail($userCourse->user?->email, new CourseFailed($userCourse, $course));
            }
        }

        return $this->ok([
            'completed' => true,
            'passed' => $passed,
            'percentage' => $percentage,
            'pass_threshold' => CourseAccessService::PASS_THRESHOLD,
            'total_correct' => $totalCorrect,
            'total_questions' => $totalQuestions,
            'time_spent' => $timeSpent,
            'review' => $review,
        ] + $this->attemptsPayload($userCourse->refresh()));
    }

    public function reset(Request $request, Course $course): JsonResponse
    {
        $userCourse = $this->access->resolve($request->user(), $course);
        $exam = $this->findExam($course);

        if ((int) $userCourse->aprobado === 1) {
            return response()->json(['success' => false, 'message' => 'You already have the course approved.'], 422);
        }

        $this->ensureAttemptsLeft($userCourse);

        $attempt = UserCourseExam::where('user_course_id', $userCourse->id)->where('exam_id', $exam->id)->first();

        // Sólo se limpia un intento a medias (no entregado); los entregados ya cuentan.
        if ($attempt && (int) $attempt->complete !== 1) {
            UserCourseExamResult::where('user_course_exam_id', $attempt->id)->where('attempt_number', (int) $attempt->intentos + 1)->delete();
            $attempt->update(['tiempo' => null, 'evento' => null]);
        } elseif ($attempt) {
            $attempt->update(['complete' => 0, 'tiempo' => null, 'evento' => null]);
        }

        return $this->ok(['message' => 'Exam attempt has been reset.'] + $this->attemptsPayload($userCourse));
    }

    /** Regla 5: tres intentos; al tercer fallo toca reiniciar el curso o comprarlo de nuevo. */
    private function ensureAttemptsLeft(UserCourse $userCourse): void
    {
        if ($this->access->examLocked($userCourse)) {
            $message = $this->access->canRestart($userCourse)
                ? 'You have used your ' . CourseAccessService::MAX_EXAM_ATTEMPTS . ' exam attempts. You can restart the course once and retake it within ' . CourseAccessService::RETAKE_DAYS . ' days.'
                : 'You have used your ' . CourseAccessService::MAX_EXAM_ATTEMPTS . ' exam attempts. Please purchase the course again to take it.';

            abort(403, $message);
        }
    }

    private function attemptsPayload(UserCourse $userCourse): array
    {
        $state = $this->access->state($userCourse);

        return [
            'attempts_used' => $state['exam_attempts_used'],
            'attempts_left' => $state['exam_attempts_left'],
            'max_attempts' => $state['exam_max_attempts'],
            'exam_locked' => $state['status'] === 'exam_locked',
            'can_restart' => $state['can_restart'],
            'can_repurchase' => $state['can_repurchase'],
        ];
    }

    private function ensureAllChaptersReady(Course $course, UserCourse $userCourse): void
    {
        $chapters = Chapter::where('course_id', $course->id)->orderBy('order')->get();

        foreach ($chapters as $chapter) {
            abort_if(
                ! $this->access->chapterCompleted($userCourse, $chapter),
                403,
                'You must complete every chapter before taking the final exam.',
            );
        }
    }

    private function findExam(Course $course): Exam
    {
        $examCourse = ExamCourse::where('course_id', $course->id)->first();

        abort_if(! $examCourse, 404, 'This course does not have a final exam configured.');

        return Exam::findOrFail($examCourse->exam_id);
    }

    private function ok(mixed $data, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $data], $status);
    }

    /** Un fallo del correo no puede tumbar la entrega del examen: se registra y sigue. */
    private function sendMail(?string $to, \Illuminate\Mail\Mailable $mailable): void
    {
        if (! filled($to)) {
            return;
        }

        try {
            Mail::to($to)->send($mailable);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    /** El intento abierto, si lo hay: entregado no cuenta. */
    private function attemptInProgress(UserCourse $userCourse, Exam $exam): ?UserCourseExam
    {
        $attempt = UserCourseExam::where('user_course_id', $userCourse->id)->where('exam_id', $exam->id)->first();

        return $attempt && (int) $attempt->complete !== 1 ? $attempt : null;
    }

    /**
     * Cierra el intento abierto cuyo plazo venció y lo cuenta como fallido. Devuelve true
     * si lo hizo. Antes esto dependía del aviso del navegador, así que cerrar la ventana
     * dejaba el intento sin contar (#1824).
     */
    private function closeAttemptIfTimeIsUp(UserCourse $userCourse, Exam $exam): bool
    {
        $attempt = $this->attemptInProgress($userCourse, $exam);

        if (! $attempt || ! $attempt->started_at || now()->lt($this->deadline($attempt, $exam))) {
            return false;
        }

        $attempt->update([
            'intentos' => (int) $attempt->intentos + 1,
            'tiempo' => '00:00:00',
            'resultado' => 0,
            'complete' => 1,
            'evento' => 'excedio',
            'started_at' => null,
        ]);

        $userCourse->update(['aprobado' => 0, 'intentos' => (int) $userCourse->intentos + 1]);
        $userCourse->refresh();

        return true;
    }

    private function deadline(UserCourseExam $attempt, Exam $exam): \Illuminate\Support\Carbon
    {
        return $attempt->started_at->copy()->addSeconds($this->durationSeconds($exam));
    }

    private function durationSeconds(Exam $exam): int
    {
        return $exam->duration ? ((int) $exam->duration * 60) : 7200;
    }

    private function durationLabel(Exam $exam): string
    {
        return gmdate('H:i:s', $this->durationSeconds($exam));
    }

    /** Lo que tardó el alumno en el intento, en HH:MM:SS, como el "Your time" de producción. */
    private function timeSpent(UserCourseExam $attempt, Exam $exam): string
    {
        if (! $attempt->started_at) {
            return '00:00:00';
        }

        $spent = (int) $attempt->started_at->diffInSeconds(now());

        return gmdate('H:i:s', min(max(0, $spent), $this->durationSeconds($exam)));
    }

    /** Lo que queda de examen según el reloj del servidor, en HH:MM:SS. */
    private function remainingTime(?UserCourseExam $attempt, Exam $exam): ?string
    {
        if (! $attempt || (int) $attempt->complete === 1) {
            return null;
        }

        if (! $attempt->started_at) {
            return $attempt->tiempo;
        }

        return gmdate('H:i:s', max(0, (int) ceil(now()->diffInSeconds($this->deadline($attempt, $exam), false))));
    }
}
