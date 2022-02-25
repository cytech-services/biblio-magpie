<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'book_id',
        'format',
        'path',
        'size',
    ];

    protected $appends = ['url'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'size' => 'integer',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function getUrlAttribute()
    {
        return Storage::disk('books')->url($this->path);
    }
}
