@extends('layouts.app')

@section('title', 'Domov')

@section('content')

    <h1>Recept : {{ $recipe->namephp }}</h1>
    <form action="{{ route('recipes.store', $recipe->id) }}" method="POST" class="mb-4">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Názov položky</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="mb-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="amount" class="form-label">Množstvo</label>
                    <input type="number" class="form-control" id="amount" name="amount" min="1" required>
                </div>
                <div class="col-md-6">
                    <label for="amount_unit" class="form-label">Jednotka</label>
                    <select class="form-select" id="amount_unit" name="amount_unit" required>
                        <option value="g">g</option>
                        <option value="ml">ml</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Pridať položku</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Názov</th>
                <th>Množstvo</th>
                <th>Počet kalórií</th>
                <th>Obrázok</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($recipe->recipe_items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->amount.' '.$item->amount_unit }}</td>
                    <td>{{ $item->calories.' kcal'}}</td>
                    <td>
                        <img src="{{ $item->image }}" style="width: 24px; height: 24px; vertical-align: middle;">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
