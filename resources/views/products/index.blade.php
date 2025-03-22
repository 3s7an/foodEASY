<!DOCTYPE html>
<html>
<head>
    <title>Nákupný zoznam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Nákupný zoznam: {{ $shoppingList->name }}</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Formulár -->
        <form action="{{ route('products.store', $shoppingList->id) }}" method="POST" class="mb-4">
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
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>