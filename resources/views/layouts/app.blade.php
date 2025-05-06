<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>foodEASY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg shadow-lg" style="background: linear-gradient(to right, #007bff, #0056b3);">
            <div class="container-fluid px-4">
                <a class="navbar-brand text-white fw-bold d-flex align-items-center" href="{{ url('/') }}">
                    <i class="fas fa-utensils me-2"></i> foodEASY
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Prepnúť navigáciu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link text-white px-3 py-2 {{ request()->routeIs('recipes.index') ? 'active' : '' }}"
                                href="{{ route('recipes.index') }}">
                                <i class="fas fa-book-open me-1"></i> Recepty
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white px-3 py-2 {{ request()->routeIs('plans.index') ? 'active' : '' }}"
                                href="{{ route('plans.index') }}">
                                <i class="fas fa-calendar-alt me-1"></i> Stravovacie plány
                            </a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center">
                        @auth
                            <ul class="navbar-nav">
                                <li class="nav-item dropdown">
                                  <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Dropdown
                                  </a>
                                  <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                  </ul>
                                </li>
                              </ul>
                        @else
                            <a class="btn btn-outline-light me-2 px-4 py-2" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Prihlásiť sa
                            </a>
                            <a class="btn btn-light px-4 py-2" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i> Zaregistrovať sa
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-grow-1">
        <div id="app" class="container py-4">
            @yield('content')
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">© {{ date('Y') }} foodEASY. Všetky práva vyhradené.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

@section('styles')
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }

        .navbar {
            padding: 1.2rem 0;
        }

        .navbar-brand {
            font-size: 1.8rem;
            color: #f8f9fa !important;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
            color: #ffffff !important;
        }

        .nav-link {
            font-weight: 500;
            color: #f8f9fa !important;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff !important;
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff !important;
        }

        .btn-outline-light,
        .btn-light {
            border-radius: 0.5rem;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .btn-outline-light:hover {
            transform: translateY(-2px);
            background-color: #f8f9fa;
            color: #007bff;
        }

        .btn-light:hover {
            transform: translateY(-2px);
            background-color: #e9ecef;
            color: #007bff;
        }

        .dropdown-menu {
            border-radius: 0.75rem;
            background-color: #fff;
            padding: 0.5rem 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item {
            padding: 0.5rem 1.5rem;
            transition: background-color 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #f1f3f5;
        }

        .navbar-toggler {
            transition: transform 0.3s ease;
        }

        .navbar-toggler:hover {
            transform: scale(1.1);
        }

        .navbar-collapse {
            transition: all 0.3s ease;
        }

        footer {
            font-size: 0.9rem;
        }
    </style>
@endsection
