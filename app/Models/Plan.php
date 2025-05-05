<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'duration', 'date_from', 'date_to'];

    public function recipes(){
        return $this->belongsToMany(Recipe::class, 'plans_recipes')
        ->withPivot('date')
        ->orderByPivot('date');
    }

    public function recipes_count(){
        if ($this->relationLoaded('recipes')) {
            return $this->recipes->unique('recipe_id')->count();
        }
    
        return $this->recipes()
            ->select('recipe_id')
            ->distinct()
            ->count('recipe_id');
    }
    

}
