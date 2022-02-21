<?php

namespace App\Http\Controllers;

use App\Http\Resources\Tables\BookResource;
use App\Models\Book;
use App\Models\Library;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Library/Index')->with('success', 'Hello!');
    }

    public function fetch_books(Request $request, Library $library)
    {
        return response()->json(
            BookResource::collection(
                Book::with([
                    'library',
                    'authors',
                    'media.file_format',
                    'categories',
                    'series',
                    'publisher',
                ])
                    ->orderBy('title')
                    ->get()
            ),
            200
        );
    }
}
