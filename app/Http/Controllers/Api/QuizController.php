<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\ChapterQuiz;
use App\Models\ChapterQuizOption;
use App\Models\Course;
use App\Models\UserCourse;
use App\Models\UserCourseChapter;
use App\Models\UserCourseChapterQuiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show(Request $request, Course $course, Chapter $chapter): JsonResponse
    {
        $userCourse = $this->findUserCourse($request, $course);
        $userCourseChapter = UserCourseChapter::firstOrCreate([
            'user_course_id' => $userCourse->id,
            'chapter_id' => $chapter->id,
        ]);

        // Matches legacy behaviour: reloading the quiz clears previous answers unless it was already passed.
        if ((int) $userCourseChapter->quiz_result !== 1) {
            UserCourseChapterQuiz::where('user_course_chapter_id', $userCourseChapter->id)->delete();
        }

        $questions = ChapterQuiz::where('chapter_id', $chapter->id)->orderBy('id')->with('options')->get();

        return $this->ok([
            'passed' => (int) $userCourseChapter->quiz_result === 1,
            'total_questions' => $questions->count(),
            'questions' => $questions->map(fn (ChapterQuiz $question) => [
                'id' => $question->id,
                'question' => $question->question,
                'options' => $question->options->map(fn (ChapterQuizOption $option) => [
                    'id' => $option->id,
                    'option' => $option->option,
                ])->values(),
            ])->values(),
        ]);
    }

    public function answer(Request $request, Course $course, Chapter $chapter): JsonResponse
    {
        $validated = $request->validate([
            'chapter_quiz_id' => ['required', 'integer'],
            'chapter_quiz_option_id' => ['required', 'integer'],
        ]);

        $userCourse = $this->findUserCourse($request, $course);
        $userCourseChapter = UserCourseChapter::firstOrCreate([
            'user_course_id' => $userCourse->id,
            'chapter_id' => $chapter->id,
        ]);

        $question = ChapterQuiz::where('id', $validated['chapter_quiz_id'])->where('chapter_id', $chapter->id)->firstOrFail();
        $option = ChapterQuizOption::where('id', $validated['chapter_quiz_option_id'])
            ->where('chapter_quiz_id', $question->id)
            ->firstOrFail();

        UserCourseChapterQuiz::updateOrCreate(
            [
                'user_course_chapter_id' => $userCourseChapter->id,
                'chapter_quiz_id' => $question->id,
            ],
            [
                'quiz_question_option_id' => $option->id,
                'result' => (int) $option->estado === 1 ? 1 : 0,
                'timequiz' => now(),
            ],
        );

        return $this->ok($this->resultPayload($chapter, $userCourseChapter));
    }

    public function result(Request $request, Course $course, Chapter $chapter): JsonResponse
    {
        $userCourse = $this->findUserCourse($request, $course);
        $userCourseChapter = UserCourseChapter::where('user_course_id', $userCourse->id)
            ->where('chapter_id', $chapter->id)
            ->firstOrFail();

        return $this->ok($this->resultPayload($chapter, $userCourseChapter));
    }

    public function reset(Request $request, Course $course, Chapter $chapter): JsonResponse
    {
        $userCourse = $this->findUserCourse($request, $course);
        $userCourseChapter = UserCourseChapter::where('user_course_id', $userCourse->id)
            ->where('chapter_id', $chapter->id)
            ->first();

        if ($userCourseChapter) {
            UserCourseChapterQuiz::where('user_course_chapter_id', $userCourseChapter->id)->delete();
        }

        return $this->ok(['message' => 'Quiz answers have been reset.']);
    }

    private function resultPayload(Chapter $chapter, UserCourseChapter $userCourseChapter): array
    {
        $totalQuestions = ChapterQuiz::where('chapter_id', $chapter->id)->count();
        $totalAnswered = UserCourseChapterQuiz::where('user_course_chapter_id', $userCourseChapter->id)->count();
        $totalCorrect = UserCourseChapterQuiz::where('user_course_chapter_id', $userCourseChapter->id)->where('result', 1)->count();

        $isComplete = $totalQuestions > 0 && $totalAnswered >= $totalQuestions;
        $percentage = $totalQuestions > 0 ? round($totalCorrect * 100 / $totalQuestions, 2) : 0;

        if ($isComplete && $percentage > 75) {
            $userCourseChapter->update(['quiz_result' => 1]);
        }

        return [
            'total_questions' => $totalQuestions,
            'total_answered' => $totalAnswered,
            'total_correct' => $totalCorrect,
            'percentage' => $percentage,
            'is_complete' => $isComplete,
            'passed' => (int) $userCourseChapter->refresh()->quiz_result === 1,
        ];
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
