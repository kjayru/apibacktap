<?php

namespace App\Support;

use App\Models\Course;
use App\Models\UserCourse;
use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade\Pdf as PdfFacade;

/**
 * Qué plantilla y qué arte le toca a cada curso. Lo usan el panel y la descarga del
 * alumno, para que el certificado sea el mismo por los dos caminos (#1832).
 */
class CertificateTemplate
{
    public static function forCourse(Course $course): string
    {
        return match ((int) $course->certification_id) {
            1 => 'pdf.certificado1',
            2 => 'pdf.certificado2',
            3 => 'pdf.certificado3',
            4 => 'pdf.certificado4',
            default => 'pdf.index',
        };
    }

    /**
     * Dompdf lee el arte del disco. Antes se le pasaba una URL armada con env('APP_URL'),
     * que viene vacía cuando la configuración está cacheada, y el certificado salía con
     * el recuadro de imagen rota.
     */
    public static function artworkPath(?string $image): string
    {
        if (! filled($image)) {
            return '';
        }

        $relative = ltrim($image, '/');
        $local = public_path($relative);

        return is_file($local) ? $local : rtrim((string) config('app.url'), '/') . '/' . $relative;
    }

    /**
     * El certificado de una matrícula aprobada, listo para descargar o adjuntar.
     *
     * Ojo con setOptions(): su segundo argumento decide si lo que se pasa se suma a la
     * configuración de dompdf o la sustituye entera, y por defecto la sustituye. Al
     * sustituirla se pierde el `chroot`, dompdf deja de poder abrir imágenes del disco
     * y el certificado sale con el recuadro de imagen rota (#1832).
     */
    public static function make(UserCourse $userCourse): PDF
    {
        $course = $userCourse->course;

        return PdfFacade::loadView(self::forCourse($course), [
            'curso' => $course,
            'user' => $userCourse->user->loadMissing('profile'),
            'user_course' => $userCourse,
            'certificado' => self::artworkPath($course->certification?->image),
        ])->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 180,
        ], true)->setPaper('a4');
    }

    /** El nombre con el que el alumno se descarga o recibe su certificado. */
    public static function fileName(UserCourse $userCourse): string
    {
        return \Illuminate\Support\Str::slug($userCourse->course->titulo) . '-certificate.pdf';
    }
}
