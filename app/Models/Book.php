<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'library_id',
        'title',
        'sub_title',
        'description',
        'edition',
        'language',
        'page_count',
        'publisher_id',
        'rating',
        'publish_date',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'rating' => 'integer',
    ];

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function identifications()
    {
        return $this->hasMany(Identification::class);
    }

    public function readHistories()
    {
        return $this->hasMany(ReadHistory::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function series()
    {
        return $this->belongsToMany(Series::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function library()
    {
        return $this->belongsTo(Library::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function thumbnail_image()
    {
        return $this->hasOne(Image::class)->ofMany([
            'id' => 'max',
        ], function ($query) {
            $query->where('format', 'thumbnail');
        });
    }

    public function small_image()
    {
        return $this->hasOne(Image::class)->ofMany([
            'id' => 'max',
        ], function ($query) {
            $query->where('format', 'small');
        });
    }
}
