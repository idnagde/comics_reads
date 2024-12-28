<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = ['name'];

    public function novels()
    {
        return $this->belongsToMany(Novel::class, 'genre_novel');
    }
}
