<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeItem extends Model
{
    protected $fillable = ['name', 'calories', 'fat', 'saturated_fat', 'cholesterol', 'total_carbohydrate', 'sugar', 'protein', 'weight', 'weight_cooked','weight_unit', 'image', 'recipe_id'];
}
