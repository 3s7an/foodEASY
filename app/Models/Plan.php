<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name','date_from', 'date_to'];

    public function recipes(){
        return $this->belongsToMany(Recipe::class, 'plans_recipes');
    }
}
