<?php

use App\Controllers\AdminController;
use App\Controllers\HomeController;
use App\Controllers\AuthenticationsController;
use App\Controllers\MushroomsController;
use App\Controllers\QuizzesController;
use Core\Router\Route;

// Authentication
Route::get('/login', [AuthenticationsController::class, 'new'])->name('users.login');
Route::post('/login', [AuthenticationsController::class, 'authenticate'])->name('users.authenticate');

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('root');

    // QUIZZ CRUD
    Route::get('/quizzes', [QuizzesController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/page/{page}', [QuizzesController::class, 'index'])->name('quizzes.paginate');
    Route::get('/quizzes/new', [QuizzesController::class, 'new'])->name('quizzes.new');
    Route::post('/quizzes', [QuizzesController::class, 'create'])->name('quizzes.create');
    Route::get('/quizzes/{id}', [QuizzesController::class, 'show'])->name('quizzes.show');
    Route::get('/quizzes/{id}/edit', [QuizzesController::class, 'edit'])->name('quizzes.edit');
    Route::put('/quizzes/{id}/update', [QuizzesController::class, 'update'])->name('quizzes.update');
    Route::delete('/quizzes/{id}', [QuizzesController::class, 'destroy'])->name('quizzes.destroy');

    Route::get('/logout', [AuthenticationsController::class, 'destroy'])->name('users.logout');
});

Route::middleware('admin')->group(function () {
    // MUSH CRUD
    Route::get('/mushrooms', [MushroomsController::class, 'index'])->name('mushrooms.index');
    Route::get('/mushrooms/page/{page}', [MushroomsController::class, 'index'])->name('mushrooms.paginate');
    Route::get('/mushrooms/new', [MushroomsController::class, 'new'])->name('mushrooms.new');
    Route::post('/mushrooms', [MushroomsController::class, 'create'])->name('mushrooms.create');
    Route::get('/mushrooms/{id}', [MushroomsController::class, 'show'])->name('mushrooms.show');
    Route::get('/mushrooms/{id}/edit', [MushroomsController::class, 'edit'])->name('mushrooms.edit');
    Route::put('/mushrooms/{id}/update', [MushroomsController::class, 'update'])->name('mushrooms.update');
    Route::delete('/mushrooms/{id}', [MushroomsController::class, 'destroy'])->name('mushrooms.destroy');

    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
});
