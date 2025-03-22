<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/shopping-list/{shoppingListId}/products', [ProductController::class, 'index'])->name('products.index');
Route::post('/shopping-list/{shoppingListId}/products', [ProductController::class, 'store'])->name('products.store');
