@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100" style="margin-top: -50px;">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 animate__animated animate__fadeIn">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0">{{ __('Váš profil') }}</h4>
            </div>

            <div class="card-body p-4">
                <div class="mb-4">
                    <label class="form-label fw-medium">{{ __('Meno') }}</label>
                    <p class="form-control form-control-lg bg-light" readonly>{{ $user->name }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-medium">{{ __('E-mailová adresa') }}</label>
                    <p class="form-control form-control-lg bg-light" readonly>{{ $user->email }}</p>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg px-4">{{ __('Späť na domov') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
@endsection
@endsection