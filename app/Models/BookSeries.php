<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookSeries extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'series_id',
        'book_id',
        'order',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'order' => 'integer',
    ];

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
