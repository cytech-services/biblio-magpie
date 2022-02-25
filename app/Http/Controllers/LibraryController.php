<?php

namespace App\Http\Controllers;

use App\Http\Resources\Tables\BookResource;
use App\Models\Book;
use App\Models\Library;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $limit = 5;
        if ($request->exists('limit')) {
            $limit = $request->input('limit');
        }

        $books = QueryBuilder::for(Book::class)
            ->with([
                'library',
                'authors',
                'media.file_format',
                'categories',
                'series',
                'publisher',
                'thumbnail_image',
                'small_image',
                'identifications' => function ($query) {
                    $query->with([
                        'identificationType' => function ($query) {
                            $query->orderPreference();
                        }
                    ]);
                }
            ])
            ->defaultSort('title')
            ->allowedSorts([
                'library.name',
                'title',
                'sub_title',
                'authors.name',
                'categories.name',
                'series.name',
                'publisher.name',
                'publish_date',
            ])
            ->allowedFilters([
                'library.name',
                'title',
                'sub_title',
                'authors.name',
                'categories.name',
                'series.name',
                'publisher.name',
            ])
            ->paginate($limit)
            ->withQueryString();


        // $books = BookResource::collection(
        //     Book::with([
        //         'library',
        //         'authors',
        //         'media.file_format',
        //         'categories',
        //         'series',
        //         'publisher',
        //         'images',
        //         'identifications' => function ($query) {
        //             $query->with([
        //                 'identificationType' => function ($query) {
        //                     $query->orderPreference();
        //                 }
        //             ]);
        //         }
        //     ])
        //         ->orderBy('title')
        //         ->get()
        // );

        return Inertia::render('Library/Index', [
            'books' => $books
        ]);
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
                    'images',
                    'identifications' => function ($query) {
                        $query->with([
                            'identificationType' => function ($query) {
                                $query->orderPreference();
                            }
                        ]);
                    }
                ])
                    ->orderBy('title')
                    ->get()
            ),
            200
        );
    }
}
