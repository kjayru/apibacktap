<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\CourseOrder;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Aviso a TAP de que un alumno compró un curso (ficha #200): sale del webhook de Stripe,
 * que es el único punto donde el pago ya está confirmado.
 */
class CourseOrderPlaced extends Mailable
{
    public function __construct(
        public readonly CourseOrder $order,
        public readonly Course $course,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New course purchase: ' . $this->course->titulo);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.course-order-placed');
    }
}
