<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeProcedure extends Model
{
    protected $fillable = ['recipe_id', 'name'];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
