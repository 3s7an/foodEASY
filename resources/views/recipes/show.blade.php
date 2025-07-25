@extends('layouts.app')

@section('title', 'Domov')
@section('content')
    <div class="container-fluid px-5" style="max-width: 60%; margin: 0 auto;">
        <div class="text-center mb-4">
            <h1 class="fw-bold text-dark mb-4 text-center" style="font-size: 3.5rem;">{{ $recipe->name }}</h1>
        </div>
        <!-- Obrazok  -->
        <div class="card shadow-sm border-0 h-100 mb-4">
            <div class="card-body d-flex justify-content-center align-items-center p-4" style="min-height: 300px;">
                @if ($recipe->getFirstMediaUrl('images'))
                <img 
                    src="{{ $recipe->getFirstMediaUrl('images') }}" 
                    alt="Obrázok receptu" 
                    class="img-fluid rounded shadow"
                    style="max-height: 280px; object-fit: cover;"
                />
                @else
                <form action="{{ route('recipe.upload_image', $recipe->id) }}" method="POST"
                        enctype="multipart/form-data" 
                        class="d-flex flex-column align-items-center gap-3 w-100">
                    @csrf
                    <input type="file" name="image" class="form-control form-control-sm w-75" accept="image/*">
                    <button type="submit" class="btn btn-primary btn-sm">Nahrať</button>
                </form>
                @endif
            </div>
        </div>


        <!-- Polozky receptu -->
        <div class="card shadow-sm border-0 h-100 mb-4">
            <div class="card-body">
                <h2 class="h5 fw-semibold mb-4 text-primary">Položky receptu</h2>

                @if ($recipe->recipe_items->count())
                    <ul class="list-unstyled">
                        @foreach ($recipe->recipe_items as $item)
                            <li class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $item->image }}" alt="{{ $item->name }}"
                                        class="rounded-circle shadow-sm"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                    <div class="ms-3">
                                        <h5 class="fw-semibold mb-1">{{ $item->name }}</h5>
                                        <div class="text-muted">
                                            <span>{{ $item->weight }} {{ $item->weight_unit }}</span> |
                                            <span>{{ $item->calories }} kcal</span>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('recipes_item.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Naozaj chceš vymazať túto ingredienciu?')"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 m-0"
                                        style="border: none; background: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="alert alert-info text-center">
                        Žiadne položky zatiaľ nepridané.
                    </div>
                @endif

                <form action="{{ route('recipes.item_store', $recipe->id) }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">Názov položky</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="amount" class="form-label fw-medium">Množstvo</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="1"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="amount_unit" class="form-label fw-medium">Jednotka</label>
                            <select class="form-select" id="amount_unit" name="amount_unit" required>
                                <option value="g">g</option>
                                <option value="ml">ml</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Pridať položku</button>
                </form>
            </div>
        </div>

        <!-- Postup -->
        <div class="card shadow-sm border-0 h-100 mb-4">
            <div class="card-body">
                <h2 class="h5 fw-semibold mb-4 text-primary">Postup:</h2>

                <ol class="list-group list-group-numbered mb-4">
                    @foreach ($recipe->procedures as $procedure)
                        <li class="list-group-item d-flex align-items-start">
                            <div class="fw-medium"> {{ $procedure->name }}</div>
                        </li>
                    @endforeach
                </ol>

                <form action="{{ route('recipe.procedure_store', $recipe->id) }}" method="post">
                    @csrf
                    <div class="input-group">
                        <input type="text" class="form-control" id="procedure_name" name="procedure_name"
                            placeholder="Napíš krok..." required>
                        <button type="submit" class="btn btn-primary">Pridať krok</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Výživové údaje -->
        <div class="card shadow-sm border-0 h-100 mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="h5 fw-semibold mb-4 text-primary">Výživové údaje</h2>
                        <ul class="list-unstyled">
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="fw-medium">Kalórie:</span>
                                <span>{{ $total_calories }} kcal</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="fw-medium">Celková hmotnosť:</span>
                                <span>{{ $total_weight }} g</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="fw-medium">Kcal / 100 g</span>
                                <span>{{ $kcal_on_100 }} kcal</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="fw-medium">Tuky:</span>
                                <span>{{ $total_fat }} g</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="fw-medium">Nasýtené mastné kyseliny:</span>
                                <span>{{ $total_saturated_fat }} g</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="fw-medium">Sacharidy:</span>
                                <span>{{ $total_carbohydrate }} g</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="fw-medium">Z toho cukry:</span>
                                <span>{{ $total_sugar }} g</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="fw-medium">Bielkoviny:</span>
                                <span>{{ $total_protein }} g</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="fw-medium">Cholesterol:</span>
                                <span>{{ $total_cholesterol }} mg</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h2 class="h5 fw-semibold mb-4 text-primary text-center">Rozdelenie výživových hodnôt</h2>
                        <div class="d-flex justify-content-center align-items-center"
                            style="max-width: 380px; max-height: 380px; margin: 0 auto;">
                            <nutrition-chart :nutrition-data="{{ json_encode($nutrition_data) }}"></nutrition-chart>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
