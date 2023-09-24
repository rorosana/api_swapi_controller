<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Characters extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'height', 'mass', 'eye_color', 'birth_year', 'gender'];

     public function skinColor()
    {
        return $this->belongsTo(SkinColor::class, 'skin_id');
    }

    public function hairColor()
    {
        return $this->belongsTo(HairColor::class, 'hair_id');
    }
}
