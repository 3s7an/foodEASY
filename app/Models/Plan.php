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
            return $this->recipes->unique('recipe_id')->count();
        }
    
        return $this->recipes()
            ->select('recipe_id')
            ->distinct()
            ->count('recipe_id');
    }
    

}
