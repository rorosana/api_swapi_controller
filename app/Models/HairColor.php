<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HairColor extends Model
{
    use HasFactory;

     public function characters()
    {
        return $this->hasMany(Character::class, 'hair_id');
    }
}
