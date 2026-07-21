<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserCourse;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function show(UserCourse $userCourse)
    {
        abort_unless((bool) $userCourse->aprobado, 404);

        $course = $userCourse->course;
        $user = $userCourse->user;
        $certification = $course?->certification;

        abort_unless($course && $user && $certification, 404);

        $view = match ((int) $course->certification_id) {
            1 => 'pdf.certificado1',
            2 => 'pdf.certificado2',
            3 => 'pdf.certificado3',
            4 => 'pdf.certificado4',
            default => 'pdf.index',
        };

        return Pdf::loadView($view, [
            'curso' => $course,
            'user' => $user,
            'user_course' => $userCourse,
            'certificado' => $certification->image,
        ])->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 180,
        ])->setPaper('a4')->stream('certificate-'.$userCourse->id.'.pdf');
    }
}
