<?php

namespace App\Http\Controllers;

use App\Models\AiClassifier;
use App\Models\ListCategory;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\RecipeItem;
use App\Models\RecipeProcedure;
use App\Models\ShoppingList;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecipeController extends Controller
{
  public function index(Request $request) {
    // $categories = RecipeCategory::all();
    /* AJAX */
    if ($request->wantsJson()) {
      if($request->filter == 'mine' && FacadesAuth::check()){
        $recipes = Recipe::with('recipe_items')->where('created_user', FacadesAuth::id())->orderBy('created_at', 'desc')->get();
      } elseif($request->filter == 'newest') {
        $recipes = Recipe::with('recipe_items')->orderBy('created_at', 'desc')->get(); 
      } elseif($request->filter == 'oldest'){
        $recipes = Recipe::with('recipe_items')->orderBy('created_at', 'asc')->get();
      } else {
        $recipes = Recipe::with('recipe_items')->get();
      }
        return response()->json($recipes);
    } 

    /* BACKEND */
    $recipes = Recipe::orderBy('created_at', 'asc')->get()->map(function ($recipe) {
      return [
        'id' => $recipe->id,
        'name' => $recipe->name,
        'image_url' => $recipe->hasMedia('images')
            ? $recipe->getFirstMediaUrl('images')
            : '/images/no-image.png',
        ];
    });


    // dd($recipes);

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

    $nutrition_data = [
      'Tuky'                     => $total_fat,
      'Nasýtené mastné kyseliny' => $total_saturated_fat,
      'Sacharidy'                => $total_carbohydrate,
      'Cukry'                    => $total_sugar,
      'Bielkoviny'               => $total_protein
    ];
    
    if(!empty($total_calories) && !empty($total_weight)){
      $kcal_on_100 = round(($total_calories / $total_weight) * 100);
    }

    return view('recipes.show', compact('recipe', 'total_calories', 'total_fat', 'total_saturated_fat', 'total_cholesterol', 'total_carbohydrate', 'total_sugar', 'total_protein', 'total_weight', 'kcal_on_100', 'nutrition_data'));
  }


 public function create(Request $request){
    $data = $request->validate([
        'name' => 'required|string|max:100',
        'items' => 'required|array',
        'steps' => 'array',
    ]);

    // dd($data);

    $recipe = Recipe::create([
      'name' => $data['name'],
      'created_user' => FacadesAuth::id() ?? '21', 
    ]);

    foreach ($data['items'] as $item) {
      RecipeItem::create([
        'recipe_id' => $recipe->id,
        'name' => $item['name'],
        'weight' => $item['weight'],
        'weight_cooked' => $item['weight_cooked'],
        'weight_unit' => $item['unit'],
        'calories' => $item['calories'],
        'fat' => $item['fat'],
        'saturated_fat' => $item['saturated_fat'],
        'cholesterol' => $item['cholesterol'],
        'total_carbohydrate' => $item['total_carbohydrate'],
        'sugar' => $item['sugar'],
        'protein' => $item['protein'],
        'image' => $item['image'] ?? '',
      ]);
    }

    if(count($data['steps']) > 0){
      foreach ($data['steps'] as $step) {
        $recipe->procedures()->create(['name' => $step]);
      }
    }

    return response()->json(['success' => true, 'id' => $recipe->id]);
  }


  public function item_store(Request $request, $recipeId){
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

  public function get_nutrients(Request $request){
    $request->validate([
        'name'        => 'required|min:3|max:40',
        'amount'      => 'required|integer',
        'amount_unit' => 'required'
    ]);

    $translated = AiClassifier::translateProductName($request->name);
    $data = AiClassifier::getNutrients($translated, $request->amount);

    if($data != null){
      $nutrients = [
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
      ];
    }

    if (!$nutrients) {
      return response()->json([
          'success' => false,
          'message' => 'Výživové hodnoty sa nepodarilo získať.'
      ], 404);
    }

    return response()->json([
      'success' => true,
      'nutrients' => $nutrients
    ]);
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

    $recipe->addMedia($request->file('image'))->toMediaCollection('images');

    return redirect()->back()->with('success', 'Obrázok bol úspešne nahraný!');
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

  public function destroy($recipe_id){
    try {
        $recipe = Recipe::findOrFail($recipe_id);

        // Odpojenie vzťahu plans, ak existuje
        $recipe->plans()->detach();

        $recipe->delete();

        Cache::forget('recipes_w_items');

        return response()->json([
            'success' => true,
            'message' => 'Recept bol úspešne vymazaný.'
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Recept neexistuje.'
        ], 404);

    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Pri mazaní receptu nastala chyba.'
        ], 500);
    }
  }


  public function item_destroy($recipe_item_id){
    try{
      $recipe_item = RecipeItem::find($recipe_item_id);

      if (!$recipe_item) {
          return redirect()->back()->with('error', 'Položka receptu nebola nájdený.');
      }


      $recipe_item->delete();

      return redirect()->back();
    
    } catch(ModelNotFoundException $e){
      return redirect()->back()->with('error', 'Plán neexistuje.');
    
    } catch(\Exception $e){
      Log::error($e->getMessage());
      return redirect()->back()->with('error', 'Pri mazaní plánu nastala chyba.');
    }
  }
}
