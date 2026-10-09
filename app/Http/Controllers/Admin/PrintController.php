<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseOrder;
use App\Models\Information;
use App\Models\User;
use App\Models\UserSign;
use Illuminate\Contracts\View\View;

/**
 * Pantallas pensadas para imprimir: sin el panel alrededor, en una hoja y con el
 * diálogo de impresión abierto al cargar (#1735, #1737, #1740).
 */
class PrintController extends Controller
{
    public function order(CourseOrder $courseOrder): View
    {
        return view('print.order', [
            'order' => $courseOrder->load('user.profile'),
        ]);
    }

    public function applicant(Information $information): View
    {
        return view('print.applicant', [
            'applicant' => $information->load(['education', 'references', 'employments', 'military', 'disclaimer.archivos']),
        ]);
    }

    public function enrollment(User $user): View
    {
        return view('print.enrollment', [
            'user' => $user,
            'sign' => UserSign::where('user_id', $user->getKey())->latest('id')->first(),
        ]);
    }
}
