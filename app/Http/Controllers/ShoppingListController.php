<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\ShoppingList;
use App\Models\ListCategory;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    public function index(){
        $shoppingLists = ShoppingList::with('products')->get();
        return view('shopping_lists.index', compact('shoppingLists'));
      }
    
      public function show($shoppingListId){
        $shoppingList = ShoppingList::with('products')->findOrFail($shoppingListId);
        return view('shopping_lists.show', compact('shoppingList'));
      }
    
      public function store(Request $request, $shoppingListId){
        $request->validate([
          'name' => 'required|string|max:255',
          'quantity' => 'required|integer|min:1',
        ]);

    
        $productName = Product::translateProductName($request->name);
        $categoryName = Product::getCategoryFromApi($productName);
        $category = ListCategory::where('name', $categoryName)->first();
    
        if (!$category) {
          return redirect()->back()->with('error', 'Kategória nebola nájdená.');
        }
    
        Product::create([
          'name' => $request->name,
          'quantity' => $request->quantity,
          'shopping_list_id' => $shoppingListId,
          'list_category_id' => $category->id,
        ]);
    
        return redirect()->route('shopping_lists.show', $shoppingListId)->with('success', 'Produkt pridaný!');
      }
}
