<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PlanRecipe extends Pivot
{
  protected $table = 'plans_recipes';
  
  protected $fillable = [
    'plan_id',
    'recipe_id',
    'category_id',
    'date',
    'user_id',
    'food_type',
  ];

  public function plan (){
    return $this->belongsTo(Plan::class);
  }

  public function recipe(){
    return $this->belongsTo(Recipe::class);
  }
}
