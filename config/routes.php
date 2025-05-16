<?php

use App\Controllers\AdminController;
use App\Controllers\HomeController;
use App\Controllers\AuthenticationsController;
use App\Controllers\MushroomController;


use Core\Router\Route;

// Authentication
Route::get('/login', [AuthenticationsController::class, 'new'])->name('users.login');
Route::post('/login', [AuthenticationsController::class, 'authenticate'])->name('users.authenticate');

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('root');
    Route::get('/mushrooms', [MushroomController::class, 'index'])->name('mushrooms.index');

    Route::get('/logout', [AuthenticationsController::class, 'destroy'])->name('users.logout');
});

Route::middleware('admin')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
});
