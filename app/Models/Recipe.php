<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Recipe extends Model implements HasMedia
{
  use HasFactory;
  use InteractsWithMedia;

  protected $fillable = ['name', 'time', 'food_type', 'category_id', 'calories', 'created_user'];

  protected $appends = ['image_url'];

  public function getImageUrlAttribute(){
      return $this->getFirstMediaUrl('images');
  }


  public function recipe_items()
  {
    return $this->hasMany(RecipeItem::class);
  }

  public function procedures()
  {
    return $this->hasMany(RecipeProcedure::class);
  }

  public function plans()
  {
    return $this->belongsToMany(Plan::class, 'plans_recipes', 'recipe_id', 'plan_id')
      ->withPivot('category_id', 'date', 'food_type')
      ->using(PlanRecipe::class);
  }

  public function registerMediaCollections(): void{
    $this->addMediaCollection('images')->singleFile();
  }



  // public function get_image_url()
  // {
  //   if ($this->image) {
  //     return url('storage/' . $this->image);
  //   }
  //   return null;
  // }
}
