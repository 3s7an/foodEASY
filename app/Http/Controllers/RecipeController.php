<?php

namespace App\Http\Controllers;

use App\Models\Recipe;

class RecipeController extends Controller
{
  public function index(){
    $recipes = Recipe::with('recipe_items')->get();
    return view('recipes.index', compact('recipes'));
  }

  public function show($recipeId){
    $shoppingList = Recipe::with('recipe_items')->findOrFail($recipeId);
    return view('recipes.show', compact('shoppingList'));
  }
}
