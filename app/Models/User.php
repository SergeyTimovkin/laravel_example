<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'user';

    public function phone()
    {
        return $this->hasMany(Phone::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}