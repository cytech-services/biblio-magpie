<?php

use App\Http\Controllers\LibraryController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

Route::redirect('/', '/library');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('library', LibraryController::class)->middleware(['auth', 'verified']);

// Route::resource('user', UserController::class)->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->group(
    function () {
        // Route::resource('user', UserController::class);

        Route::controller(UserController::class)->group(function () {
            Route::get('/user/settings/{user}', 'settings')->name('user.settings');
            Route::put('/user/settings/{user}', 'update')->name('user.settings');
        });
    }
);

// Include auth routes
require __DIR__ . '/auth.php';
