@extends('layouts.app')

@section('title', 'Recepty')
    
    @section('content')
    <div class="container mt-4">
        <h1>Recepty</h1>
        <ul class="list-group">
            @foreach($recipes as $recipe)
                <li class="list-group-item">
                    <a href="{{ route('recipes.show', $recipe->id) }}">
                        {{ $recipe->name ?? 'Zoznam #' . $recipe->id }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

@endsection