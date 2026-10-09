<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Bienvenida al crear la cuenta, con los cursos más recientes (#1831). */
class WelcomeRegistered extends Mailable
{
    public function __construct(public readonly User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome TAP Security');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.welcome-registered', with: [
            'name' => $this->user->name,
            'courses' => Course::orderByDesc('id')->take(3)->get(),
        ]);
    }
}
