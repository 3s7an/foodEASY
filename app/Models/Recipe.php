<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'procedure', 'image', 'category_id'];

    public function recipe_items(){
        return $this->hasMany(RecipeItem::class);
    }

    public function procedures()
    {
        return $this->hasMany(RecipeProcedure::class);
    }

    public function plans(){
        return $this->belongsToMany(Plan::class, 'plans_recipes');
    }



    public function get_image_url(){
        if($this->image){
            return url('storage/' . $this->image);
        }
        return null;
    }
}
