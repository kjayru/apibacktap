<?php

namespace App\Mail;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseOrder;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Confirmación de compra para el alumno. En producción este correo estaba comentado y
 * nadie lo recibía; el cliente lo pidió con su diseño (#1831).
 */
class CoursePurchased extends Mailable
{
    public function __construct(
        public readonly CourseOrder $order,
        public readonly Course $course,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Congratulations');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.course-purchased', with: [
            'name' => $this->order->name ?: $this->order->user?->name,
            'course' => $this->course,
            'chapters' => Chapter::where('course_id', $this->course->id)->count(),
            'amount' => $this->order->amount ?? $this->order->price,
            'url' => rtrim((string) config('app.frontend_url'), '/') . '/learn/' . $this->course->slug,
        ]);
    }
}
