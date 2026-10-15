<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\CertificateTemplate;
use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CertificateController extends Controller
{
    public function show(Request $request, Course $course): Response
    {
        // El certificado es de la matrícula aprobada, aunque después el alumno haya vuelto
        // a comprar el curso y su última matrícula esté en curso o caducada. Con
        // `?enrollment=` se pide el de una matrícula concreta, que es como lo enlaza la
        // lista de cursos cuando hay varias compras del mismo curso (#1876).
        $validated = $request->validate(['enrollment' => ['nullable', 'integer']]);

        $userCourse = isset($validated['enrollment'])
            ? UserCourse::where('id', (int) $validated['enrollment'])
                ->where('user_id', $request->user()->id)
                ->where('course_id', $course->id)
                ->first()
            : UserCourse::where('user_id', $request->user()->id)
                ->where('course_id', $course->id)
                ->where('aprobado', 1)
                ->latest('id')
                ->first();

        abort_if(! $userCourse, 404, 'Enrollment not found.');
        abort_if((int) $userCourse->aprobado !== 1, 422, 'You must pass the final exam before downloading the certificate.');

        // El alumno se descarga el certificado oficial de TAP, el mismo que sirve el
        // panel: hasta ahora recibía una plantilla genérica y sin el arte, porque la
        // imagen se buscaba en /storage y los certificados viven en /certs (#1832).
        abort_if(! $course->certification, 422, 'This course does not have a certificate assigned yet.');

        return CertificateTemplate::make($userCourse)
            ->stream(CertificateTemplate::fileName($userCourse));
    }
}
