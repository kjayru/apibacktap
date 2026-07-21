<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CertificateController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('user-courses/{userCourse}/certificate', [CertificateController::class, 'show'])
        ->name('user-courses.certificate');
});
