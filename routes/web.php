<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\Web\BokkingController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\StripeController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', [BokkingController::class, 'index'])->name('dashboard');

    // Rutas CRUD de caballos
    Route::resource('horse', HorseController::class);

    // Rutas CRUD de reservas
    Route::resource('bookings', BokkingController::class);

    // Ruta para descargar el PDF del detalle de la reserva
    Route::get('/bookings/{id}/pdf', [BokkingController::class, 'showPdf'])->name('bookings.show.pdf');
});

// Email 
Route::get('/email', [EmailController::class, 'create']);
Route::post('/email', [EmailController::class, 'sendEmail'])->name('send.email');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth:sanctum')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth:sanctum'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth:sanctum'])->name('verification.send');
