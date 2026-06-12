<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::view('/', 'auth.login')->name('home');
Route::view('/dashboard', 'pages.dashboard', [
    'userEmail' => session('email'),
    'userName' => session('name'),
])->name('dashboard');

Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.submit');
Route::get('/register', [UserController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [UserController::class, 'register'])->name('register.submit');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/settings', [UserController::class, 'showSettingsForm'])->name('settings');
Route::post('/settings', [UserController::class, 'updateSettings'])->name('settings.update');

Route::get('/auth/google', [UserController::class, 'googleRedirect'])->name('auth.google.redirect');

Route::get('/task-list', [App\Http\Controllers\StudentController::class, 'showTaskList'])->name('task.list');
Route::get('/tasks', [App\Http\Controllers\TaskController::class, 'index'])->name('tasks.index');
Route::post('/task-list/link', [App\Http\Controllers\StudentController::class, 'linkForm'])->name('task.list.link');
Route::post('/task-list/sync', [App\Http\Controllers\StudentController::class, 'syncStudents'])->name('task.list.sync');
Route::post('/tasks/sync', [App\Http\Controllers\TaskController::class, 'syncTasks'])->name('tasks.sync');

Route::fallback(function () {
    return redirect()->route('login');
});
