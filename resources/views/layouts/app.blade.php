<!DOCTYPE html>
<html>

<head>
    <title>Nákupný zoznam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('shopping_lists.index') }}">Nákupné zoznamy</a>
            </div>
        </nav>

        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('recipes.index') }}">Recepty</a>
            </div>
        </nav>
    </header>


    <main>
        <div class="container mt-5">
            @yield('content')
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
