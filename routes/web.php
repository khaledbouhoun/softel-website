<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Portal\AccountController;
use App\Http\Controllers\Portal\AuthController;
use App\Http\Controllers\Portal\BookingController;
use App\Http\Controllers\Portal\InterventionController;
use App\Http\Controllers\Portal\PasswordController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

// Redirect root to default locale
Route::get('/', function () {
    return redirect('/'.config('app.locale', 'en'));
});

Route::prefix('{locale}')
    ->where(['locale' => 'en|fr|ar'])
    ->middleware(['set.locale'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');

        Route::get('/about', [HomeController::class, 'about'])->name('about');

        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

        Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
        Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

        // Customer portal (all data comes from the CRM portal API, server-side).
        Route::prefix('portal')->group(function () {
            Route::get('/sign-in', [AuthController::class, 'showLogin'])->name('portal.login');
            Route::get('/sign-up', [AuthController::class, 'showRegister'])->name('portal.register');
            Route::get('/forgot-password', [PasswordController::class, 'request'])->name('portal.password.request');
            Route::get('/reset-password/{token}', [PasswordController::class, 'reset'])->name('portal.password.reset');

            // Google OAuth
            Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('portal.auth.google');
            Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('portal.auth.google.callback');

            // E-mail verification
            Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('portal.verify.email');

            Route::middleware('throttle:portal-auth')->group(function () {
                Route::post('/sign-in', [AuthController::class, 'login'])->name('portal.login.store');
                Route::post('/sign-up', [AuthController::class, 'register'])->name('portal.register.store');
                Route::post('/forgot-password', [PasswordController::class, 'email'])->name('portal.password.email');
                Route::post('/reset-password', [PasswordController::class, 'update'])->name('portal.password.update');
                Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('portal.verify.email.store');
                Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->name('portal.verify.resend');
            });

            Route::middleware('portal.auth')->group(function () {
                Route::post('/sign-out', [AuthController::class, 'logout'])->name('portal.logout');

                Route::get('/', [AccountController::class, 'dashboard'])->name('portal.dashboard');

                Route::get('/profile', [AccountController::class, 'profile'])->name('portal.profile');
                Route::patch('/profile', [AccountController::class, 'updateProfile'])->name('portal.profile.update');

                Route::get('/request', [BookingController::class, 'create'])->name('portal.request.create');
                Route::post('/request', [BookingController::class, 'store'])->name('portal.request.store');

                Route::get('/requests', [AccountController::class, 'requests'])->name('portal.requests.index');
                Route::get('/requests/{id}', [AccountController::class, 'showRequest'])->whereNumber('id')->name('portal.requests.show');
                Route::post('/requests/{id}/cancel', [AccountController::class, 'cancelRequest'])->whereNumber('id')->name('portal.requests.cancel');

                Route::get('/appointments', [AccountController::class, 'appointments'])->name('portal.appointments.index');
                Route::post('/appointments/{id}/accept', [AccountController::class, 'acceptAppointment'])->whereNumber('id')->name('portal.appointments.accept');
                Route::post('/appointments/{id}/decline', [AccountController::class, 'declineAppointment'])->whereNumber('id')->name('portal.appointments.decline');

                Route::get('/trials', [AccountController::class, 'trials'])->name('portal.trials');
                Route::get('/products', [AccountController::class, 'products'])->name('portal.products');
                Route::get('/updates', [AccountController::class, 'updates'])->name('portal.updates');
                Route::get('/downloads', [AccountController::class, 'downloads'])->name('portal.downloads');
                Route::get('/downloads/{id}', [AccountController::class, 'downloadRelease'])->whereNumber('id')->name('portal.downloads.file');

                Route::get('/support', [InterventionController::class, 'index'])->name('portal.interventions.index');
                Route::get('/support/new', [InterventionController::class, 'create'])->name('portal.interventions.create');
                Route::post('/support', [InterventionController::class, 'store'])->name('portal.interventions.store');
                Route::get('/support/{id}', [InterventionController::class, 'show'])->whereNumber('id')->name('portal.interventions.show');
                Route::post('/support/{id}/messages', [InterventionController::class, 'message'])->whereNumber('id')->name('portal.interventions.message');
                Route::post('/support/{id}/transition', [InterventionController::class, 'transition'])->whereNumber('id')->name('portal.interventions.transition');
                Route::post('/support/{id}/attachments', [InterventionController::class, 'attach'])->whereNumber('id')->name('portal.interventions.attach');
                Route::get('/support/{id}/attachments/{attachmentId}', [InterventionController::class, 'download'])->whereNumber(['id', 'attachmentId'])->name('portal.interventions.download');
                Route::get('/support/{id}/attachments/{attachmentId}/preview', [InterventionController::class, 'preview'])->whereNumber(['id', 'attachmentId'])->name('portal.interventions.preview');
            });
        });
    });
