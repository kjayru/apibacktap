<?php

namespace App\Support;

use App\Models\Course;

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
}
