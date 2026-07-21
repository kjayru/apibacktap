<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\LearnController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PublicSiteController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\StripeWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::get('/exist-profile', [AuthController::class, 'existProfile']);
        });
    });

    Route::get('/home', [PublicSiteController::class, 'home']);
    Route::get('/home/posts', [PublicSiteController::class, 'homePosts']);
    Route::get('/home/categories', [PublicSiteController::class, 'homeCategories']);
    Route::get('/services', [PublicSiteController::class, 'services']);
    Route::get('/industries', [PublicSiteController::class, 'industries']);
    Route::get('/industries/{categorySlug}', [PublicSiteController::class, 'industriesByCategory']);
    Route::get('/industries/{categorySlug}/{industrySlug}', [PublicSiteController::class, 'industryDetail']);
    Route::get('/training/events', [PublicSiteController::class, 'trainingEvents']);
    Route::get('/blog', [PublicSiteController::class, 'blog']);
    Route::get('/blog/{slug}', [PublicSiteController::class, 'blogDetail']);
    Route::post('/contact', [PublicSiteController::class, 'contact']);
    Route::post('/employment', [PublicSiteController::class, 'employment']);
    Route::post('/form8850', [PublicSiteController::class, 'form8850']);
    Route::get('/courses', [PublicSiteController::class, 'courses']);
    Route::get('/courses/{slug}', [PublicSiteController::class, 'courseDetail']);

    Route::middleware('auth:sanctum')->prefix('learn')->group(function (): void {
        Route::get('/my-courses', [LearnController::class, 'myCourses']);
        Route::get('/sign-status', [LearnController::class, 'signStatus']);
        Route::post('/sign', [LearnController::class, 'sign']);
        Route::get('/courses/{course:slug}/verify-access', [LearnController::class, 'verifyAccess']);
        Route::get('/courses/{course:slug}', [LearnController::class, 'courseDetail']);
        Route::post('/courses/{course:slug}/restart', [LearnController::class, 'restartCourse']);
        Route::get(
            '/courses/{course:slug}/chapters/{chapter:slug}/contents/{content:slug}',
            [LearnController::class, 'contentDetail'],
        )->withoutScopedBindings();
        Route::post(
            '/courses/{course:slug}/chapters/{chapter:slug}/contents/{content:slug}/complete',
            [LearnController::class, 'completeContent'],
        )->withoutScopedBindings();

        Route::get('/courses/{course:slug}/chapters/{chapter:slug}/quiz', [QuizController::class, 'show'])->withoutScopedBindings();
        Route::post('/courses/{course:slug}/chapters/{chapter:slug}/quiz', [QuizController::class, 'answer'])->withoutScopedBindings();
        Route::get('/courses/{course:slug}/chapters/{chapter:slug}/quiz/result', [QuizController::class, 'result'])->withoutScopedBindings();
        Route::post('/courses/{course:slug}/chapters/{chapter:slug}/quiz/reset', [QuizController::class, 'reset'])->withoutScopedBindings();

        Route::get('/courses/{course:slug}/exam', [ExamController::class, 'show']);
        Route::post('/courses/{course:slug}/exam', [ExamController::class, 'submit']);
        Route::post('/courses/{course:slug}/exam/reset', [ExamController::class, 'reset']);

        Route::get('/courses/{course:slug}/certificate', [CertificateController::class, 'show']);
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/cart/quote', [CartController::class, 'quote']);
        Route::post('/checkout/session', [CheckoutController::class, 'create']);
        Route::get('/orders/by-session', [CheckoutController::class, 'showBySession']);

        Route::get('/profile', [ProfileController::class, 'show']);
        Route::post('/profile', [ProfileController::class, 'store']);
    });

    Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle']);
});

Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());
