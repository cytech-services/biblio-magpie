<?php

use App\Http\Controllers\LibraryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(LibraryController::class)->group(function () {
    Route::get('/library/fetch_books/{library?}', 'fetch_books')->name('library.books.fetch');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
