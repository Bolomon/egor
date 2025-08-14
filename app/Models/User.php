<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'api_token',
    ];

    protected $hidden = [
        'api_token',
    ];

    public function quests()
    {
        return $this->belongsToMany(Quest::class)->withPivot('completed')->withTimestamps();
    }
}
