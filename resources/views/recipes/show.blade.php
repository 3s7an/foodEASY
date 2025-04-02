@extends('layouts.app')

@section('title', 'Domov')

@section('content')
    <div class="container my-5">
        <!-- Hlavička s názvom receptu a nahrávaním obrázka -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h1 class="fw-bold">Recept: {{ $recipe->name }}</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <form action="{{ route('recipe.upload_image', $recipe->id) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center justify-content-md-end gap-3">
                    @csrf
                    <div>
                        <img src="{{ $recipe->get_image_url() }}" alt="Obrázok receptu" class="img-thumbnail" style="max-width: 100px; height: auto;">
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <button type="submit" class="btn btn-primary">Nahrať obrázok</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Formulár na pridanie položky -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h2 class="h5 fw-semibold mb-3">Pridať položku do receptu</h2>
                <form action="{{ route('recipes.store', $recipe->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">Názov položky</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="amount" class="form-label fw-medium">Množstvo</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="1" required>
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

        <!-- Tabuľka s položkami receptu -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h5 fw-semibold mb-3">Položky receptu</h2>
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Názov</th>
                            <th scope="col">Množstvo</th>
                            <th scope="col">Počet kalórií</th>
                            <th scope="col">Obrázok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recipe->recipe_items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->amount . ' ' . $item->amount_unit }}</td>
                                <td>{{ $item->calories . ' kcal' }}</td>
                                <td>
                                    <img src="{{ $item->image }}" alt="{{ $item->name }}" class="img-fluid rounded" style="max-width: 40px; height: auto;">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Žiadne položky zatiaľ nepridané.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection