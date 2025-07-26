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
    <style>
        @media (max-width: 991.98px) {

            main {
                margin-left: 0 !important;
            }

            .navbar-brand,
            .navbar-toggler {
                margin-bottom: 0.5rem;
            }
        }

        @media (min-width: 992px) {
            main {
                margin-left: 200px;
            }
        }
    </style>
</head>

<body style="font-family: 'Roboto', sans-serif; background-color: #f8f9fa;">

<nav class="navbar navbar-expand-lg shadow fixed-top"
     style="background: linear-gradient(to right, #d2a00f, #e9b61d); z-index: 1030; height: 56px;">
  <div class="container-fluid h-100 d-flex align-items-center justify-content-between px-3 px-sm-4">

    <a class="navbar-brand text-white fw-bold d-flex align-items-center mb-sm-4 h-100" href="{{ url('/') }}">
      <i class=""></i> foodEASY
    </a>

    <div class="d-flex align-items-center">

      <button class="navbar-toggler d-lg-none border-0 p-0 me-2"
              type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas"
              aria-controls="sidebarOffcanvas"
              style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end d-none d-lg-flex">
        <div class="d-flex align-items-center">
          @auth
          <div class="dropdown">
            <a class="nav-link text-white dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown"
               role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" aria-labelledby="profileDropdown">
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
  </div>
</nav>


    <aside class="d-none d-lg-flex position-fixed bg-light border-end flex-column align-items-start py-4 px-3"
           style="top: 56px; left: 0; height: calc(100vh - 56px); width: 200px; z-index: 1020;">
        <a href="{{route('home')}}" class="sidebar-link mb-3"><i class="fas fa-home me-2"></i> Domov</a>
        <a href="{{ route('recipes.index') }}" class="sidebar-link mb-3 {{ request()->routeIs('recipes.index') ? 'active' : '' }}"><i class="fas fa-book me-2"></i> Recepty</a>
        <a href="{{ route('plans.index') }}" class="sidebar-link mb-3 {{ request()->routeIs('plans.index') ? 'active' : '' }}"><i class="fas fa-calendar-alt me-2"></i> Jedálničky</a>
    </aside>

    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebarOffcanvas" style="width: 200px;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <a href="{{route('home')}}" class="sidebar-link mb-3"><i class="fas fa-home me-2"></i> Domov</a>
            <a href="{{ route('recipes.index') }}" class="sidebar-link mb-3 {{ request()->routeIs('recipes.index') ? 'active' : '' }}"><i class="fas fa-book me-2"></i> Recepty</a>
            <a href="{{ route('plans.index') }}" class="sidebar-link mb-3 {{ request()->routeIs('plans.index') ? 'active' : '' }}"><i class="fas fa-calendar-alt me-2"></i> Jedálničky</a>
        </div>
    </div>


    <main class="pt-4" style="margin-top: 70px; padding-left: 1rem; padding-right: 1rem;">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
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

            <div id="app" class="container py-4">
                @yield('content')
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">© {{ date('Y') }} foodEASY. Všetky práva vyhradené.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>

</html>
