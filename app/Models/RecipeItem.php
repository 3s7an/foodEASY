<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeItem extends Model
{
    protected $fillable = ['name', 'calories', 'amount', 'recipe_id'];
}
