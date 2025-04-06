@extends('layouts.app')

@section('title', 'Recepty')

@section('content')
    <div class="container mt-4" id="recipe-app">
      <div class="d-flex align-items-center justify-content-between">
        <h1 >Recepty</h1>
        <button class="btn btn-sm btn-info ml-4" @click="modal_add_recipe()" style="height: 30%">
          Pridať do zoznamu
      </button>  
      </div>
        <ul class="list-group mt-4">
            @foreach ($recipes as $recipe)
                <li class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('recipes.show', $recipe->id) }}">
                            {{ $recipe->name ?? 'Zoznam #' . $recipe->id }}
                        </a>
                        <button class="btn btn-sm btn-success" @click="modal_recipe_to_list('{{ addslashes($recipe->name) }}', {{ $recipe->id }})">
                          Pridať do zoznamu
                      </button>                   
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="modal fade" id="add_recipe_modal" tabindex="-1" aria-labelledby="add_recipe_modal_label" aria-hidden="true">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="add_recipe_modal_label" v-text="modalTitle"></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="{{ route('recipe.add_to_list') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="recipe_name" class="form-label fw-medium">Názov receptu</label>
                            <input type="text" name="recipe_name" id="recipe_name" class="form-control" placeholder="Zadaj názov receptu" required>
                        </div>
                    </div>
                
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
                        <button type="submit" class="btn btn-primary">Pridať</button>
                    </div>
                </form>
                

              </div>
          </div>
      </div>

        <div class="modal fade" id="recipe_to_list_modal" tabindex="-1" aria-labelledby="recipe_to_list_modal_label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="recipe_to_list_modal_label" v-text="modalTitle"></h5>
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
