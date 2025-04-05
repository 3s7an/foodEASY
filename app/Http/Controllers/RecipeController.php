<?php

namespace App\Http\Controllers;

use App\Models\AiClassifier;
use App\Models\ListCategory;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\RecipeProcedure;
use App\Models\ShoppingList;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
  public function index(){
    $recipes = Recipe::with('recipe_items')->get();
    $shoppingLists = ShoppingList::with('products')->get();
    return view('recipes.index', compact('recipes', 'shoppingLists'));
  }

  public function show($recipeId){
    $recipe = Recipe::with('recipe_items')->findOrFail($recipeId);

    $total_calories       = NULL;
    $total_fat            = NULL;
    $total_saturated_fat  = NULL;
    $total_cholesterol    = NULL;
    $total_carbohydrate   = NULL;
    $total_sugar          = NULL;
    $total_protein        = NULL;
    $total_weight         = NULL;
    $kcal_on_100          = NULL;
    

    foreach($recipe->recipe_items as $item){
      $total_calories += $item->calories;
      $total_fat += $item->fat;
      $total_saturated_fat += $item->saturated_fat;
      $total_cholesterol += $item->cholesterol;
      $total_carbohydrate += $item->total_carbohydrate;
      $total_sugar += $item->sugar;
      $total_protein += $item->protein;
      if($item->weight_cooked != 0){
        $total_weight += $item->weight_cooked;
      } else {
        $total_weight += $item->weight;
      }
    }
    
    if(!empty($total_calories) && !empty($total_weight)){
      $kcal_on_100 = round(($total_calories / $total_weight) * 100);
    }

    return view('recipes.show', compact('recipe', 'total_calories', 'total_fat', 'total_saturated_fat', 'total_cholesterol', 'total_carbohydrate', 'total_sugar', 'total_protein', 'total_weight', 'kcal_on_100'));
  }

  public function store(Request $request, $recipeId){
    $request->validate([
      'name'        => 'required|min:3|max:40',
      'amount'      => 'required|integer',
      'amount_unit' => 'required'
    ]);

    $product_name = $request->name;
    $amount = $request->amount;

    $product_name = AiClassifier::translateProductName($product_name);



    $data = AiClassifier::getNutrients($product_name, $amount);


    if($data != null){
      RecipeItem::create([
        'name'                => $request->name,
        'weight'              => $data['weight'],
        'weight_cooked'       => $data['weight_cooked'],
        'weight_unit'         => $request->amount_unit,
        'calories'            => $data['calories'],
        'fat'                 => $data['fat'],
        'saturated_fat'       => $data['saturated_fat'],
        'cholesterol'         => $data['cholesterol'],
        'total_carbohydrate'  => $data['total_carbohydrate'],
        'sugar'               => $data['sugar'],
        'protein'             => $data['protein'],
        'image'               => $data['image'] ?? '',
        'recipe_id'           => $recipeId
      ]);
    }

    return redirect()->route('recipes.show', $recipeId)->with('success', 'Recept bol pridaný!');
  }

  public function add_to_list(Request $request){
    
    $shopping_list_id = $request->shopping_list_id;
    $recipe_id = $request->recipe_id;
     
    $recipe = Recipe::with('recipe_items')->findOrFail($recipe_id);
    
    foreach($recipe->recipe_items as $item){

      $product_name = AiClassifier::translateProductName($item->name);
      $categoryName = AiClassifier::getCategoryFromApi($product_name);
      $category = ListCategory::where('name', $categoryName)->first();
      

      Product::create([
        'name'              => $product_name,
        'quantity'          => 1,
        'shopping_list_id'  => $shopping_list_id,
        'list_category_id'  => $category->id

      ]);
    }

    return redirect()->route('recipes.index')->with('succes', 'Položky receptu boli pridané na zoznam');
    
  }

  public function upload_image(Request $request, $recipe_id){
   
    $request->validate([
      'image' => 'required|image|max:2048' 
    ]);

    $recipe = Recipe::find($recipe_id);

    if(!$recipe){
      return redirect()->back()->with('error', 'Recept nebol nájdený.');
    }

    if ($request->hasFile('image')) {
      $file = $request->file('image');
      $fileName = time() . '.' . $file->getClientOriginalExtension();
      $file->storeAs('images', $fileName, 'public');

        
      $recipe->update([
        'image' => 'images/' . $fileName
      ]);

        return redirect()->back()->with('success', 'Obrázok bol úspešne nahraný!');
    }
  }

  public function procedure_store(Request $request, $recipe_id){
    $request->validate([
      'procedure_name'    => 'required|min:3|max:255'
    ]);
    
    $recipe = Recipe::find($recipe_id);


    if(!$recipe){
      return redirect()->back()->with('error', 'Recept nebol nájdený');
    }

    RecipeProcedure::create([
      'name'        => $request->procedure_name,
      'recipe_id'   => $recipe->id
    ]);
    return redirect()->back()->with('succes', 'Krok postupu receptu bol pridaný');
  }
}
