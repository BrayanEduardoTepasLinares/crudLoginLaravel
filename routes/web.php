<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth'])->group(function () {
    Route::resource('rols', RolController::class);
    Route::resource('users', UserController::class);
});

Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');

// Procesa el formulario de login (POST)
Route::post('/login', [UserController::class, 'login']);

// Cierra la sesión (Logout)
Route::get('/logout', [UserController::class, 'logout'])->name('logout');