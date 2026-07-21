<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Chaptercontent;
use App\Models\Course;
use App\Models\UserCourse;
use App\Models\UserCourseChapter;
use App\Models\UserCourseChapterContent;
use App\Models\UserSign;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LearnController extends Controller
{
    public function myCourses(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $userCourses = UserCourse::with('course')->where('user_id', $userId)->latest('id')->get();

        return $this->ok($userCourses->map(function (UserCourse $userCourse) use ($userId) {
            $course = $userCourse->course;

            return [
                'user_course_id' => $userCourse->id,
                'course' => [
                    'id' => $course->id,
                    'title' => $course->titulo,
                    'slug' => $course->slug,
                    'banner_url' => $this->assetUrl($course->banner),
                    'level' => $course->nivel,
                ],
                'progress_percent' => UserCourseChapter::completeChapter($userId, $course->id, $userCourse->id),
                'days_left' => $userCourse->dias_activo !== null ? max(0, (int) round(UserCourse::dayleft($userCourse->id))) : null,
                'approved' => (bool) $userCourse->aprobado,
                'finished' => (bool) $userCourse->finalizado,
                'expired' => (bool) $userCourse->caducado,
            ];
        })->values());
    }

    public function courseDetail(Request $request, Course $course): JsonResponse
    {
        $userCourse = $this->findUserCourse($request, $course);

        $chapters = Chapter::where('course_id', $course->id)->orderBy('order')->get();
        $completedChapterIds = UserCourseChapter::where('user_course_id', $userCourse->id)
            ->pluck('id', 'chapter_id');

        $previousCompleted = true;
        $chapterPayload = $chapters->values()->map(function (Chapter $chapter, int $index) use ($userCourse, $completedChapterIds, &$previousCompleted) {
            $hasQuiz = $chapter->chapterquizzes()->exists() || filled($chapter->quiz);
            $totalContents = $chapter->chaptercontents()->count();
            $completedContentIds = $completedChapterIds->has($chapter->id)
                ? UserCourseChapterContent::where('user_course_chapter_id', $completedChapterIds[$chapter->id])->pluck('content_id')
                : collect();
            $completedContents = $completedContentIds->count();

            $quizPassed = UserCourseChapter::where('user_course_id', $userCourse->id)
                ->where('chapter_id', $chapter->id)
                ->where('quiz_result', 1)
                ->exists();

            $contentsDone = $totalContents > 0 && $completedContents >= $totalContents;
            $completed = $contentsDone && (! $hasQuiz || $quizPassed);

            $status = $completed ? 'completed' : ($previousCompleted ? 'active' : 'locked');
            $previousCompleted = $completed;

            return [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'slug' => $chapter->slug,
                'order' => $chapter->order,
                'has_video' => filled($chapter->video),
                'has_audio' => (bool) $chapter->audio,
                'has_reading' => (bool) $chapter->reading,
                'has_quiz' => $hasQuiz,
                'status' => $status,
                'contents_total' => $totalContents,
                'contents_completed' => $completedContents,
                'contents' => $chapter->chaptercontents->map(fn (Chaptercontent $content) => [
                    'id' => $content->id,
                    'title' => $content->titulo,
                    'slug' => $content->slug,
                    'order' => $content->order,
                    'completed' => $completedContentIds->contains($content->id),
                ])->values(),
            ];
        });

        return $this->ok([
            'user_course_id' => $userCourse->id,
            'course' => [
                'id' => $course->id,
                'title' => $course->titulo,
                'slug' => $course->slug,
                'banner_url' => $this->assetUrl($course->banner),
                'instructor' => $course->responsable,
            ],
            'progress_percent' => UserCourseChapter::completeChapter($request->user()->id, $course->id, $userCourse->id),
            'days_left' => $userCourse->dias_activo !== null ? max(0, (int) round(UserCourse::dayleft($userCourse->id))) : null,
            'approved' => (bool) $userCourse->aprobado,
            'finished' => (bool) $userCourse->finalizado,
            'chapters' => $chapterPayload,
        ]);
    }

    public function contentDetail(Request $request, Course $course, Chapter $chapter, Chaptercontent $content): JsonResponse
    {
        $this->findUserCourse($request, $course);

        return $this->ok([
            'id' => $content->id,
            'title' => $content->titulo,
            'slug' => $content->slug,
            'order' => $content->order,
            'video_url' => $this->assetUrl($content->video),
            'audio_url' => $this->assetUrl($content->audio),
            'poster_url' => $this->assetUrl($content->poster),
            'content_html' => $content->contenido,
        ]);
    }

    public function completeContent(Request $request, Course $course, Chapter $chapter, Chaptercontent $content): JsonResponse
    {
        $userCourse = $this->findUserCourse($request, $course);

        $userCourseChapter = UserCourseChapter::firstOrCreate([
            'user_course_id' => $userCourse->id,
            'chapter_id' => $chapter->id,
        ]);

        UserCourseChapterContent::firstOrCreate([
            'user_course_chapter_id' => $userCourseChapter->id,
            'content_id' => $content->id,
        ]);

        return $this->ok([
            'progress_percent' => UserCourseChapter::completeChapter($request->user()->id, $course->id, $userCourse->id),
        ]);
    }

    public function restartCourse(Request $request, Course $course): JsonResponse
    {
        $userCourse = $this->findUserCourse($request, $course);

        if ((int) $userCourse->aprobado === 1) {
            return response()->json(['success' => false, 'message' => 'You already have the course approved.'], 422);
        }

        if (UserCourse::where('parent_id', $userCourse->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'The course has already been restarted.'], 422);
        }

        $newUserCourse = UserCourse::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'fecha_inicio' => Carbon::now(),
            'dias_activo' => $userCourse->dias_activo,
            'reiniciado' => 1,
            'parent_id' => $userCourse->id,
        ]);

        return $this->ok(['message' => 'Course has been restarted.', 'user_course_id' => $newUserCourse->id]);
    }

    public function verifyAccess(Request $request, Course $course): JsonResponse
    {
        $hasAccess = UserCourse::where('user_id', $request->user()->id)->where('course_id', $course->id)->exists();

        return $this->ok(['has_access' => $hasAccess]);
    }

    public function signStatus(Request $request): JsonResponse
    {
        $sign = UserSign::where('user_id', $request->user()->id)->first();

        return $this->ok(['signed' => (bool) $sign]);
    }

    public function sign(Request $request): JsonResponse
    {
        if (UserSign::where('user_id', $request->user()->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Your signature was already registered previously.'], 422);
        }

        $validated = $request->validate([
            'legalname' => ['required', 'string', 'max:255'],
            'fullname' => ['required', 'string', 'max:255'],
            'initial' => ['required', 'string', 'max:20'],
            'firma' => ['required', 'string'],
        ]);

        $sign = UserSign::create([
            'user_id' => $request->user()->id,
            'legalname' => $validated['legalname'],
            'email' => $request->user()->email,
            'fullname' => $validated['fullname'],
            'initial' => $validated['initial'],
            'firma' => $validated['firma'],
            'code' => (string) now()->timestamp,
        ]);

        return $this->ok(['signed' => true, 'signed_at' => $sign->created_at?->toISOString()], 201);
    }

    private function findUserCourse(Request $request, Course $course): UserCourse
    {
        $userCourse = UserCourse::where('user_id', $request->user()->id)->where('course_id', $course->id)->first();

        abort_if(! $userCourse, 403, 'You do not have access to this course.');

        return $userCourse;
    }

    private function assetUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return url('/storage/' . ltrim($path, '/'));
    }

    private function ok(mixed $data, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $data], $status);
    }
}
