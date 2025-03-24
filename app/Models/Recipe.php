<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = ['name', 'procedure'];

    public function recipe_items()
    {
        return $this->hasMany(RecipeItem::class);
    }
}
