<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = ['name', 'procedure', 'image'];

    public function recipe_items()
    {
        return $this->hasMany(RecipeItem::class);
    }

    public function get_image_url(){
        if($this->image){
            return url('storage/' . $this->image);
        }
        return 'https://via.placeholder.com/800x400';
    }
}
