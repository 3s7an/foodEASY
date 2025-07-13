<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
  protected $fillable =
  [
    'user_id',
    'name',
    'generation_mode',
    'days',
    'date_start',
    'date_stop',
    'calories',
    'meat_percentage'
  ];

  public function recipes()
  {
    return $this->belongsToMany(Recipe::class, 'plans_recipes', 'plan_id', 'recipe_id')
      ->withPivot('category_id', 'date', 'food_type')
      ->using(PlanRecipe::class);
  }

  public function recipes_count()
  {
    if ($this->relationLoaded('recipes')) {
      return $this->recipes->unique('recipe_id')->count();
    }

    return $this->recipes()
      ->select('recipe_id')
      ->distinct()
      ->count('recipe_id');
  }
}
