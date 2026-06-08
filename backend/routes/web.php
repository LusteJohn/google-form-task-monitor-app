<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::view('/', 'auth.login')->name('home');
Route::view('/dashboard', 'pages.dashboard', [
    'userEmail' => session('email'),
    'userName' => session('name'),
])->name('dashboard');

Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');

Route::fallback(function () {
    return redirect()->route('login');
});
Route::post('/login', [UserController::class, 'login'])->name('login.submit');
Route::get('/register', [UserController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [UserController::class, 'register'])->name('register.submit');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/settings', [UserController::class, 'showSettingsForm'])->name('settings');
Route::post('/settings', [UserController::class, 'updateSettings'])->name('settings.update');

Route::get('/auth/google', [UserController::class, 'googleRedirect'])->name('auth.google.redirect');
