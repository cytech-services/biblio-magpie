<?php

use App\Http\Controllers\LibraryController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\TasksController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

Route::redirect('/', '/library');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('library', LibraryController::class)->middleware(['auth', 'verified']);

// Route::controller(LibraryController::class)->group(function () {
//     Route::get('/library', 'index')->name('library.index');
// });

// Include auth routes
require __DIR__ . '/auth.php';
