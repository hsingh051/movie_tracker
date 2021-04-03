<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMovie extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'movie_id',
        'user_id',
        'liked',
        'watched',
    ];

}
