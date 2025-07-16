<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>foodEASY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<meta name="csrf-token" content="{{ csrf_token() }}">

@if (session('success'))
  <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Zavrieť"></button>
  </div>
@endif

@if(session('status') === 'error')
  <div class="alert alert-danger">
    @foreach ($errors->all() as $error)
      <div>{{ $error }}</div>
    @endforeach
  </div>
@endif


<body style="overflow: hidden; font-family: 'Roboto', sans-serif; background-color: #f8f9fa;">
    <nav class="navbar navbar-expand-lg shadow-lg fixed-top" style="background: linear-gradient(to right, #d2a00f, #e9b61d); z-index: 1030; height: 50px;">
        <div class="container-fluid px-4">
            <!-- Logo naľavo -->
            <a class="navbar-brand text-white fw-bold d-flex align-items-center" href="{{ url('/') }}">
                <i class="fas fa-utensils me-2"></i> foodEASY
            </a>
    
            <!-- Hamburger tlačidlo -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Prepnúť navigáciu">
                <span class="navbar-toggler-icon"></span>
            </button>
    
            <!-- Navigácia a tlačidlá doprava -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center">
                    @auth
                    <div class="dropdown">
                        <a class="nav-link text-white dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg animate__animated animate__fadeIn"
                            aria-labelledby="profileDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user me-2"></i> Profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i> Odhlásiť sa
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
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
    
    <aside class="position-fixed bg-light border-end d-flex flex-column align-items-start py-4 px-3"
        style="top: 50px; left: 0; height: calc(100vh - 70px); width: 120px; z-index: 1020;">
        <a href="#" class="sidebar-link mb-3"><i class="fas fa-home me-2 text-decoration-none"></i> Domov</a>
        <a href="{{ route('recipes.index') }}" class="sidebar-link mb-3 {{ request()->routeIs('recipes.index') ? 'active' : '' }} text-decoration-none"><i class="fas fa-book me-2"></i> Recepty</a>
        <a href="{{ route('plans.index') }}" class="sidebar-link mb-3 {{ request()->routeIs('plans.index') ? 'active' : '' }} text-decoration-none"><i class="fas fa-calendar-alt me-2"></i> Plány</a>
    </aside>

    <!-- Content -->
    <main style="margin-top: 70px; margin-left: 140px; height: calc(100vh - 70px); overflow-y: auto;">
        <div id="app" class="container py-4">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3" style="position: relative; z-index: 1;">
        <p class="mb-0">© {{ date('Y') }} foodEASY. Všetky práva vyhradené.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>

</html>
