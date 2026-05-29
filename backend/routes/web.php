<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::view('/', 'auth.login')->name('home');
Route::view('/dashboard', 'pages.dashboard')->name('dashboard');

Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.submit');
Route::get('/register', [UserController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [UserController::class, 'register'])->name('register.submit');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/auth/google', [UserController::class, 'googleRedirect'])->name('auth.google.redirect');
