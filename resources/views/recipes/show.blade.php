@extends('layouts.app')

@section('title', 'Domov')

@section('content')

  <h1>Recept : {{$recipe->namephp}}</h1>
    <!-- Formulár -->
    <form action="{{ route('recipes.store', $recipe->id) }}" method="POST" class="mb-4">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Názov položky</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Množstvo</label>
            <input type="number" class="form-control" id="amount" name="quantity" min="1" required>
        </div>
      
        <button type="submit" class="btn btn-primary">Pridať položku</button>
    </form>

@endsection
