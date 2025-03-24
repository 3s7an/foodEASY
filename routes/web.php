<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ShoppingListController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/shopping-list', [ShoppingListController::class, 'index'])->name('shopping_lists.index');
Route::get('/shopping-list/{shoppingListId}', [ShoppingListController::class, 'show'])->name('shopping_lists.show');
Route::post('/shopping-list/{shoppingListId}/products', [ShoppingListController::class, 'store'])->name('shopping_lists.store');

Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes/{recipesId}', [RecipeController::class, 'show'])->name('recipes.show');


