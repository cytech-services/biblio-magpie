<?php

use App\Http\Controllers\LibraryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/auth.php';

Route::resource('library', LibraryController::class)->middleware(['auth', 'verified']);


// Route::redirect('/', '/library');

// Route::controller(LibraryController::class)->group(function () {
//     Route::get('/library', 'index')->name('library.index');
// });
