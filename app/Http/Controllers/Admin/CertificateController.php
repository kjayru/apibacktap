<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\CertificateTemplate;
use App\Models\UserCourse;

class CertificateController extends Controller
{
    public function show(UserCourse $userCourse)
    {
        abort_unless((bool) $userCourse->aprobado, 404);

        $course = $userCourse->course;

        abort_unless($course && $userCourse->user && $course->certification, 404);

        return CertificateTemplate::make($userCourse)
            ->stream(CertificateTemplate::fileName($userCourse));
    }
}
