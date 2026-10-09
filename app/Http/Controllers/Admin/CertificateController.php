<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\CertificateTemplate;
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

        return Pdf::loadView(CertificateTemplate::forCourse($course), [
            'curso' => $course,
            'user' => $user->load('profile'),
            'user_course' => $userCourse,
            'certificado' => CertificateTemplate::artworkPath($certification->image),
        ])->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 180,
        ])->setPaper('a4')->stream('certificate-'.$userCourse->id.'.pdf');
    }
}
