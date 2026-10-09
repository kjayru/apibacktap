<?php

namespace App\Mail;

use App\Models\UserSign;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Confirmación de la firma del documento de enrollment (#1831). */
class EnrollmentSigned extends Mailable
{
    public function __construct(public readonly UserSign $sign)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Sign Enroll');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.enrollment-signed', with: [
            'name' => $this->sign->fullname ?: $this->sign->legalname,
            'legalName' => $this->sign->legalname,
            // Las firmas del sitio anterior no siempre guardaron el código: se usa su
            // identificador para no dejar el dato vacío.
            'documentId' => $this->sign->code ?: $this->sign->getKey(),
            'url' => rtrim((string) config('app.frontend_url'), '/') . '/profile',
        ]);
    }
}
