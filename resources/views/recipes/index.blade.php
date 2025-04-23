@extends('layouts.app')

@section('title', 'Recepty')

@section('content')
    <div class="container mt-4">
        <div class="d-flex align-items-center justify-content-between">
            <h1 class="fw-bold text-center">Recepty</h1>
            <button class="btn btn-outline-primary" @click="modal_add_recipe()" style="height: 30%">
                Nový recept
                <i class="fas fa-plus fa-xs"></i>
            </button>  
        </div>

        @include('recipes.new_recipe_modal')

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-4">
            @foreach ($recipes as $recipe)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        @if ($recipe->get_image_url())
                            <img src="{{ $recipe->get_image_url() }}" alt="Obrázok receptu" class="card-img-top" style="height: 200px; width: 100%; object-fit: cover; object-position: center;">
                        @endif
                    
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-3">
                                {{ $recipe->name ?? 'Zoznam #' . $recipe->id }}
                            </h5>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div class="d-flex gap-2">
                                    <!-- Detail (oko) -->
                                    <a href="{{ route('recipes.show', $recipe->id) }}" class="btn btn-sm btn-outline-secondary" title="Zobraziť detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <!-- Čas (hodiny) -->
                                    @if($recipe->id)
                                        <span class="badge bg-light text-dark d-flex align-items-center px-2 py-1">
                                            <i class="fas fa-clock me-1"></i> {{ $recipe->id }} min
                                        </span>
                                    @endif
                                </div>
                               {{--
                                <button class="btn btn-outline-primary btn-sm" @click="modal_recipe_to_list('{{ addslashes($recipe->name) }}', {{ $recipe->id }})" title="Pridať do nákupného zoznamu">
                                    <i class="fa fa-cart-plus"></i>
                                </button>
                                  --}}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div> 
    </div>
@endsection
