<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamCourse;
use App\Models\ExamQuestion;
use App\Models\ExamQuestionOption;
use App\Models\UserCourse;
use App\Models\UserCourseChapter;
use App\Models\UserCourseExam;
use App\Models\UserCourseExamResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    private const PASS_THRESHOLD = 75;

    public function show(Request $request, Course $course): JsonResponse
    {
        $userCourse = $this->findUserCourse($request, $course);
        $exam = $this->findExam($course);

        $this->ensureAllChaptersReady($course, $userCourse);

        $attempt = UserCourseExam::where('user_course_id', $userCourse->id)->where('exam_id', $exam->id)->first();

        $questions = ExamQuestion::where('exam_id', $exam->id)->orderBy('id')->with('examquestionoptions')->get();

        return $this->ok([
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'description' => $exam->description,
                'duration_seconds' => $exam->duration ? ((int) $exam->duration * 60) : 7200,
            ],
            'attempts_used' => $attempt->intentos ?? 0,
            'approved' => (bool) $userCourse->aprobado,
            'remaining_time' => $attempt && (int) $attempt->complete !== 1 ? $attempt->tiempo : null,
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

    public function submit(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.exam_question_id' => ['required', 'integer'],
            'answers.*.exam_question_option_id' => ['required', 'integer'],
            'remaining_time' => ['nullable', 'string', 'max:20'],
            'expired' => ['nullable', 'boolean'],
        ]);

        $userCourse = $this->findUserCourse($request, $course);
        $exam = $this->findExam($course);
        $this->ensureAllChaptersReady($course, $userCourse);

        if ((int) $userCourse->aprobado === 1) {
            return response()->json(['success' => false, 'message' => 'You already have the course approved.'], 422);
        }

        $attempt = UserCourseExam::firstOrCreate(
            ['user_course_id' => $userCourse->id, 'exam_id' => $exam->id],
            ['intentos' => 0, 'complete' => 0, 'resultado' => 0, 'tiempo' => '00:00:00'],
        );

        // The client-side countdown reaching zero: persist the event without grading (matches legacy behaviour).
        if ($validated['expired'] ?? false) {
            $attempt->update(['tiempo' => $validated['remaining_time'] ?? '00:00:00', 'resultado' => 0, 'complete' => 1, 'evento' => 'excedio']);

            return $this->ok(['completed' => false, 'expired' => true, 'message' => 'Time is up.']);
        }

        $attemptNumber = (int) $attempt->intentos + 1;

        DB::transaction(function () use ($validated, $attempt, $attemptNumber): void {
            foreach ($validated['answers'] as $answer) {
                $option = ExamQuestionOption::where('id', $answer['exam_question_option_id'])
                    ->where('exam_question_id', $answer['exam_question_id'])
                    ->first();

                UserCourseExamResult::create([
                    'user_course_exam_id' => $attempt->id,
                    'attempt_number' => $attemptNumber,
                    'exam_question_id' => $answer['exam_question_id'],
                    'exam_question_option_id' => $answer['exam_question_option_id'],
                    'result' => $option && (int) $option->resultado === 1 ? 1 : 0,
                ]);
            }

            $attempt->update(['intentos' => $attemptNumber, 'complete' => 1]);
        });

        $stats = UserCourse::porcentajes($course->id, $request->user()->id);
        $percentage = (float) ($stats->porcentaje_correcto ?? 0);
        $passed = $percentage >= self::PASS_THRESHOLD;

        $attempt->update(['resultado' => $percentage]);

        if ($passed) {
            $userCourse->update(['aprobado' => 1, 'finalizado' => 1]);
        } else {
            $userCourse->update(['aprobado' => 0, 'intentos' => ($userCourse->intentos ?? 0) + 1]);
        }

        return $this->ok([
            'completed' => true,
            'passed' => $passed,
            'percentage' => $percentage,
            'total_correct' => $stats->respuestas_correctas ?? 0,
            'total_questions' => $stats->total_respuestas ?? 0,
            'attempts_used' => $attemptNumber,
        ]);
    }

    public function reset(Request $request, Course $course): JsonResponse
    {
        $userCourse = $this->findUserCourse($request, $course);
        $exam = $this->findExam($course);

        if ((int) $userCourse->aprobado === 1) {
            return response()->json(['success' => false, 'message' => 'You already have the course approved.'], 422);
        }

        $attempt = UserCourseExam::where('user_course_id', $userCourse->id)->where('exam_id', $exam->id)->first();

        if ($attempt) {
            UserCourseExamResult::where('user_course_exam_id', $attempt->id)->where('attempt_number', $attempt->intentos)->delete();
            $attempt->update(['complete' => 0, 'tiempo' => null, 'evento' => null]);
        }

        return $this->ok(['message' => 'Exam attempt has been reset.']);
    }

    private function ensureAllChaptersReady(Course $course, UserCourse $userCourse): void
    {
        $chapters = Chapter::where('course_id', $course->id)->get();

        foreach ($chapters as $chapter) {
            $hasQuiz = $chapter->chapterquizzes()->exists() || filled($chapter->quiz);
            $userChapter = UserCourseChapter::where('user_course_id', $userCourse->id)->where('chapter_id', $chapter->id)->first();

            $contentsTotal = $chapter->chaptercontents()->count();
            $contentsDone = $userChapter
                ? $userChapter->userCourseChapterContents()->count()
                : 0;

            abort_if(
                $contentsTotal > $contentsDone || ($hasQuiz && (! $userChapter || (int) $userChapter->quiz_result !== 1)),
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

    private function findUserCourse(Request $request, Course $course): UserCourse
    {
        $userCourse = UserCourse::where('user_id', $request->user()->id)->where('course_id', $course->id)->first();

        abort_if(! $userCourse, 403, 'You do not have access to this course.');

        return $userCourse;
    }

    private function ok(mixed $data, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $data], $status);
    }
}
