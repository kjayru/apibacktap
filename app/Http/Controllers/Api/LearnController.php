<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CartException;
use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Chaptercontent;
use App\Models\Course;
use App\Models\UserCourse;
use App\Models\UserCourseChapter;
use App\Models\UserCourseChapterContent;
use App\Models\UserSign;
use App\Services\CourseAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LearnController extends Controller
{
    public function __construct(private readonly CourseAccessService $access)
    {
    }

    public function myCourses(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $userCourses = UserCourse::with('course')->where('user_id', $userId)->latest('id')->get();

        // Al listar se caducan las matrículas vencidas, como hacía el sitio anterior.
        $userCourses->each(fn (UserCourse $userCourse) => $this->access->expireIfDue($userCourse));

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
                // Sólo la matrícula más reciente de cada curso es la que se puede cursar.
                'is_current' => $userCourse->id === $this->access->latestEnrollment($userCourse->user, $course)?->id,
            ] + $this->access->state($userCourse);
        })->values());
    }

    public function courseDetail(Request $request, Course $course): JsonResponse
    {
        $userCourse = $this->access->resolve($request->user(), $course);

        $chapters = Chapter::where('course_id', $course->id)->orderBy('order')->get();
        $completedChapterIds = UserCourseChapter::where('user_course_id', $userCourse->id)
            ->pluck('id', 'chapter_id');

        $previousCompleted = true;
        $chapterPayload = $chapters->values()->map(function (Chapter $chapter) use ($userCourse, $completedChapterIds, &$previousCompleted) {
            $hasQuiz = $chapter->chapterquizzes()->exists() || filled($chapter->quiz);
            $totalContents = $chapter->chaptercontents()->count();
            $completedContentIds = $completedChapterIds->has($chapter->id)
                ? UserCourseChapterContent::where('user_course_chapter_id', $completedChapterIds[$chapter->id])->pluck('content_id')
                : collect();

            $completed = $this->access->chapterCompleted($userCourse, $chapter);
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
                'contents_completed' => $completedContentIds->count(),
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
            'chapters' => $chapterPayload,
        ] + $this->access->state($userCourse));
    }

    public function contentDetail(Request $request, Course $course, Chapter $chapter, Chaptercontent $content): JsonResponse
    {
        $this->resolveChapter($request, $course, $chapter, $content);

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
        $userCourse = $this->resolveChapter($request, $course, $chapter, $content);

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

    /** Reglas 6 y 7: reinicio único con 15 días tras agotar los intentos del examen. */
    public function restartCourse(Request $request, Course $course): JsonResponse
    {
        $userCourse = $this->access->resolve($request->user(), $course);

        try {
            $newUserCourse = $this->access->restart($userCourse);
        } catch (CartException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return $this->ok([
            'message' => 'Course has been restarted. You have ' . CourseAccessService::RETAKE_DAYS . ' days to complete it.',
            'user_course_id' => $newUserCourse->id,
        ] + $this->access->state($newUserCourse));
    }

    public function verifyAccess(Request $request, Course $course): JsonResponse
    {
        $userCourse = $this->access->latestEnrollment($request->user(), $course);

        return $this->ok([
            'has_access' => $userCourse !== null && (int) $userCourse->caducado !== 1,
            'reason' => match (true) {
                $userCourse === null => 'not_enrolled',
                (int) $userCourse->caducado === 1 => 'expired',
                default => null,
            },
        ] + ($userCourse ? $this->access->state($userCourse) : []));
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

    /** Regla 1: el contenido de un capítulo sólo se sirve con los anteriores aprobados. */
    private function resolveChapter(Request $request, Course $course, Chapter $chapter, Chaptercontent $content): UserCourse
    {
        abort_if((int) $chapter->course_id !== (int) $course->id, 404, 'Chapter not found in this course.');
        abort_if((int) $content->chapter_id !== (int) $chapter->id, 404, 'Content not found in this chapter.');

        $userCourse = $this->access->resolve($request->user(), $course);
        $this->access->assertChapterUnlocked($userCourse, $course, $chapter);

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
