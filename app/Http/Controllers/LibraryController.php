<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Library/Index')->with('success', 'Hello!');
    }
}
