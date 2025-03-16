<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'quantity', 'shopping_list_id', 'list_category_id'];

    public function shoppingList()
    {
        return $this->belongsTo(ShoppingList::class);
    }

    public function listCategory()
    {
        return $this->belongsTo(ListCategory::class);
    }
}