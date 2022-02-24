<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookCategory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'book_id',
        'genre_id',
    ];

    public $timestamps = false;

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
}
