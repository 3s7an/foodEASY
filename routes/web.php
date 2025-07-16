<?php

use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ShoppingListController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

/* Nákupný zoznam */
Route::get('/shopping-list', [ShoppingListController::class, 'index'])->name('shopping_lists.index');
Route::get('/shopping-list/{shoppingListId}', [ShoppingListController::class, 'show'])->name('shopping_lists.show');
Route::post('/shopping-list/{shoppingListId}/products', [ShoppingListController::class, 'store'])->name('shopping_lists.store');

/* Recept */
Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes/{recipesId}', [RecipeController::class, 'show'])->name('recipes.show');
Route::post('/recipes/store{recipesId}/items', [RecipeController::class, 'item_store'])->name('recipes.item_store');
Route::post('recipes/add-to-list', [RecipeController::class, 'add_to_list'])->name('recipe.add_to_list');
Route::post('recipes/{recipe_id}/upload-image', [RecipeController::class, 'upload_image'])->name('recipe.upload_image');
Route::post('recipes/{reciped_id}/procedure/store', [RecipeController::class, 'procedure_store'])->name('recipe.procedure_store');
Route::delete('/recipes/{recipe_id}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
Route::delete('/recipes/items/{recipe_item_id}', [RecipeController::class, 'item_destroy'])->name('recipes_item.destroy');


/* Stravovací plán */


Auth::routes();

Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show')->middleware('auth');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
