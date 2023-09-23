<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkinColor extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function characters()
    {
        return $this->hasMany(Character::class, 'skin_id');
    }
}
