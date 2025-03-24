@extends('layouts.app')

@section('title', 'Nákupné zoznamy')
    
    @section('content')
    <div class="container mt-4">
        <h1>Nákupné zoznamy</h1>
        <ul class="list-group">
            @foreach($shoppingLists as $shoppingList)
                <li class="list-group-item">
                    <a href="{{ route('shopping_lists.show', $shoppingList->id) }}">
                        {{ $shoppingList->name ?? 'Zoznam #' . $shoppingList->id }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

@endsection