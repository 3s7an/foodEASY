@extends('layouts.app')

@section('title', 'Recepty')

@vite('resources/js/recipes.js')

@section('content')
    <div class="container mt-4" id="recipe-app">
        <h1>Recepty</h1>
        <ul class="list-group">
            @foreach ($recipes as $recipe)
                <li class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('recipes.show', $recipe->id) }}">
                            {{ $recipe->name ?? 'Zoznam #' . $recipe->id }}
                        </a>
                        <button class="btn btn-sm btn-success" @click="openModal('{{ addslashes($recipe->name) }}', {{ $recipe->id }})">
                          Pridať do zoznamu
                      </button>                   
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="modal fade" id="recipeModal" tabindex="-1" aria-labelledby="recipeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="recipeModalLabel" v-text="modalTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                  <form action="{{ route('recipe.add_to_list') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <span>Vyber nákupný zoznam, do ktorého chceš pridať recept</span>
                        <select class="form-select mt-2" id="shoppingListSelect" name="shopping_list_id">
                            @foreach ($shoppingLists as $shoppingList)
                                <option value="{{ $shoppingList->id }}">{{ $shoppingList->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="recipe_id" v-model="selectedRecipeId">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
                        <button type="submit" class="btn btn-primary">Pridať</button>
                    </div>
                  </form>

                </div>
            </div>
        </div>
    @endsection
