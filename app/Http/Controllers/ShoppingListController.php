<?php

namespace App\Http\Controllers;

use App\Models\AiClassifier;
use App\Models\Product;
use App\Models\ShoppingList;
use App\Models\ListCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShoppingListController extends Controller
{
  public function index()
  {
    $shoppingLists = ShoppingList::with('products')->get();
    return view('shopping_lists.index', compact('shoppingLists'));
  }

  public function show($list_id)
  {
    $shoppingList = ShoppingList::with('products')->findOrFail($list_id);
    return view('shopping_lists.show', compact('shoppingList'));
  }

  public function store(Request $request, $list_id)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'quantity' => 'required|integer|min:1',
    ]);


    $productName = AiClassifier::translateProductName($request->name);
    $categoryName = AiClassifier::getCategoryFromApi($productName);
    $category = ListCategory::where('name', $categoryName)->first();

    if (!$category) {
      return redirect()->back()->with('error', 'Kategória nebola nájdená.');
    }

    Product::create([
      'name' => $request->name,
      'quantity' => $request->quantity,
      'shopping_list_id' => $list_id,
      'list_category_id' => $category->id,
    ]);

    return redirect()->route('shopping_lists.show', $list_id)->with('success', 'Produkt pridaný!');
  }

  public function destroy($list_id){
    try{
      $list = ShoppingList::find($list_id);

      if (!$list) {
          return redirect()->back()->with('error', 'Plán nebol nájdený.');
      }

      $list->delete();

      return redirect('plans.index')->with('succes', 'Plán bol úspešne vymazaný');
    
    } catch(ModelNotFoundException $e){
      return redirect()->back()->with('error', 'Plán neexistuje.');
    
    } catch(\Exception $e){
      Log::error($e->getMessage());
      return redirect()->back()->with('error', 'Pri mazaní plánu nastala chyba.');
    }
  }
}
