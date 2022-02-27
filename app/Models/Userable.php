<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Userable extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'userable_id',
        'userable_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userable()
    {
        return $this->belongsTo(Userable::class);
    }
}
