@extends('layouts.app')

    @section('title', 'Domov')
    
    @section('content')

    
    
        <h1>Nákupný zoznam: {{ $shoppingList->name }}</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Formulár -->
        <form action="{{ route('shopping_lists.store', $shoppingList->id) }}" method="POST" class="mb-4">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Názov produktu</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Množstvo</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
            </div>
            <button type="submit" class="btn btn-primary">Pridať produkt</button>
        </form>

        <!-- Zoznam produktov -->
        <table class="table">
            <thead>
                <tr>
                    <th>Názov</th>
                    <th>Množstvo</th>
                    <th>Kategória</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($shoppingList->products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>{{ $product->listCategory->name ?? 'Není přiřazena' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endsection