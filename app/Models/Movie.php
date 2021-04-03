<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'imdbID',
        'title',
        'year',
        'type',
        'poster',
    ];

    // Users Relationship
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_movies')
            ->withPivot('liked', 'watched');
    }

}
