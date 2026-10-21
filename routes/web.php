<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\PrintController;

// El dominio sólo sirve al panel, así que la raíz lleva directo al acceso en lugar de a
// la pantalla de bienvenida de Laravel, que no tenía nada que ver con TAP. Quien ya haya
// entrado no ve el formulario: el propio panel lo manda al escritorio.
Route::get('/', fn () => redirect()->route('filament.admin.auth.login'));

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('user-courses/{userCourse}/certificate', [CertificateController::class, 'show'])
        ->name('user-courses.certificate');

    // Versiones para imprimir, sin el panel alrededor (#1735, #1737, #1740).
    Route::get('print/orders/{courseOrder}', [PrintController::class, 'order'])->name('print.order');
    Route::get('print/applicants/{information}', [PrintController::class, 'applicant'])->name('print.applicant');
    Route::get('print/enrollment/{user}', [PrintController::class, 'enrollment'])->name('print.enrollment');
});
