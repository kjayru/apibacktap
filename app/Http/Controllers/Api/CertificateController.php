<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserCourse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function show(Request $request, Course $course): Response
    {
        $userCourse = UserCourse::where('user_id', $request->user()->id)->where('course_id', $course->id)->first();

        abort_if(! $userCourse, 403, 'You do not have access to this course.');
        abort_if((int) $userCourse->aprobado !== 1, 422, 'You must pass the final exam before downloading the certificate.');

        $certificationImage = null;
        if ($course->certification && filled($course->certification->image)) {
            $path = $course->certification->image;
            $certificationImage = Str::startsWith($path, ['http://', 'https://']) ? $path : url('/storage/' . ltrim($path, '/'));
        }

        $pdf = Pdf::loadView('pdf.certificate', [
            'userName' => trim($request->user()->name . ' ' . ($request->user()->lastname ?? '')),
            'courseTitle' => $course->titulo,
            'certificationImage' => $certificationImage,
            'issuedAt' => now()->format('F j, Y'),
            'certificateId' => 'TAP-' . $course->id . '-' . $userCourse->id,
        ])->setPaper('a4', 'landscape');

        $fileName = Str::slug($course->titulo) . '-certificate.pdf';

        return $pdf->stream($fileName);
    }
}
