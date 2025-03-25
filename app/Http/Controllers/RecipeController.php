<?php

namespace App\Http\Controllers;

use App\Models\AiClassifier;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
  public function index(){
    $recipes = Recipe::with('recipe_items')->get();
    return view('recipes.index', compact('recipes'));
  }

  public function show($recipeId){
    $recipe = Recipe::with('recipe_items')->findOrFail($recipeId);
    return view('recipes.show', compact('recipe'));
  }

  public function store(Request $request, $recipeId){
    $product_name = $request->name;
    $product_name = AiClassifier::translateProductName($product_name);
    $data = AiClassifier::getNutrients($product_name);
  }
}
