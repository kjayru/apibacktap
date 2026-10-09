<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Aviso al alumno de que aprobó el curso, con su certificado (#1831). */
class CoursePassed extends Mailable
{
    public function __construct(
        public readonly UserCourse $userCourse,
        public readonly Course $course,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Congratulations');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.course-passed', with: [
            'name' => $this->userCourse->user?->name,
            'course' => $this->course,
            'url' => rtrim((string) config('app.frontend_url'), '/') . '/learn/' . $this->course->slug . '/complete',
        ]);
    }
}
