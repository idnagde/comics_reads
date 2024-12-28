<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Novel extends Model
{
    protected $fillable = ['user_id', 'title', 'synopsis'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'genre_novel');
    }


    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }
}
