<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('completed')->withTimestamps();
    }
}
