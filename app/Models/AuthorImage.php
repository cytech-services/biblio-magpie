<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author_id',
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

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function getUrlAttribute()
    {
        return Storage::disk('books')->url($this->path);
    }
}
