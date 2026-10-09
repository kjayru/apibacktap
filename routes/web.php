<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\PrintController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('user-courses/{userCourse}/certificate', [CertificateController::class, 'show'])
        ->name('user-courses.certificate');

    // Versiones para imprimir, sin el panel alrededor (#1735, #1737, #1740).
    Route::get('print/orders/{courseOrder}', [PrintController::class, 'order'])->name('print.order');
    Route::get('print/applicants/{information}', [PrintController::class, 'applicant'])->name('print.applicant');
    Route::get('print/enrollment/{user}', [PrintController::class, 'enrollment'])->name('print.enrollment');
});
