<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\UserCourse;
use App\Services\CourseAccessService;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Aviso al alumno de que no aprobó. Como en producción, sale sólo al agotar los tres
 * intentos, no en cada suspenso (#1831).
 */
class CourseFailed extends Mailable
{
    public function __construct(
        public readonly UserCourse $userCourse,
        public readonly Course $course,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Course Failed');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.course-failed', with: [
            'name' => $this->userCourse->user?->name,
            'course' => $this->course,
            'retakeDays' => CourseAccessService::RETAKE_DAYS,
            'url' => rtrim((string) config('app.frontend_url'), '/') . '/learn/my-courses',
        ]);
    }
}
