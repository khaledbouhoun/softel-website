<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
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
    });
