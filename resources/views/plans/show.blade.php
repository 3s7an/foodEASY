@extends('layouts.app')

@section('title', 'Domov')

@section('content')
<div class="container-fluid px-5" style="max-width: 60%; margin: 0 auto;">
    <div class="text-center mb-4">
        <h1 class="fw-bold text-dark mb-4" style="font-size: 3.5rem;">{{ $plan->name }}</h1>
    </div>

    <div class="row g-3">
        @foreach($plan->recipes as $recipe)
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary"><a href= "{{ route('recipes.show', $recipe->id) }}">{{ $recipe->name }}</a></h5>
                        <span class="badge bg-light text-dark fs-6">
                            {{ \Carbon\Carbon::parse($recipe->pivot->date)->format('d.m.Y') }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
