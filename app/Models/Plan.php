<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name','date_from', 'date_to', 'period'];

    public function recipes(){
        return $this->belongsToMany(Recipe::class, 'plans_recipes');
    }

    public function recipes_count(){
    if ($this->relationLoaded('recipes')) {
        return $this->recipes->count();
    }

    return $this->recipes()->count();
    }

}
