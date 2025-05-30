@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100" style="margin-top: -50px;">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 animate__animated animate__fadeIn">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0">{{ __('Registrácia') }}</h4>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="form-label fw-medium">{{ __('Meno') }}</label>
                        <input id="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Zadajte vaše meno">
                        
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label fw-medium">{{ __('E-mailová adresa') }}</label>
                        <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Zadajte váš e-mail">
                        
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-medium">{{ __('Heslo') }}</label>
                        <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Zadajte vaše heslo">
                        
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password-confirm" class="form-label fw-medium">{{ __('Potvrdenie hesla') }}</label>
                        <input id="password-confirm" type="password" class="form-control form-control-lg" name="password_confirmation" required autocomplete="new-password" placeholder="Zopakujte vaše heslo">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            {{ __('Zaregistrovať sa') }}
                        </button>
                    </div>

                    @if (Route::has('login'))
                        <div class="text-center">
                            <p class="mb-0">{{ __('Už máte účet?') }} 
                                <a class="text-primary text-decoration-none fw-medium" href="{{ route('login') }}">
                                    {{ __('Prihláste sa') }}
                                </a>
                            </p>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<style>

body {
    background-color: #f8f9fa;
}

.card {
    border-radius: 0.75rem;
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.form-control-lg {
    border-radius: 0.5rem;
}

.btn-primary {
    background-color: #007bff;
    border: none;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.form-label {
    color: #495057;
}

.text-primary {
    transition: color 0.3s ease;
}

.text-primary:hover {
    color: #0056b3;
}

/* Animate.css for fade-in effect */
.animate__animated {
    animation-duration: 0.8s;
}
</style>

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
@endsection
@endsection