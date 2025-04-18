@extends('layouts.app')

@section('title', 'Stravovacie plány')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="fw-bold text-center">Stravovacie plány</h1>
        <a href="{{ route('plans.create') }}" class="btn btn-outline-primary">
            Nový stravovací plán
            <i class="fas fa-plus fa-xs"></i>
        </a>
    </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-4">
            @foreach ($plans as $plan)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title">
                                <a href="{{ route('recipes.show', $plan->id) }}" class="text-decoration-none text-dark">
                                    {{ $plan->name ?? 'Zoznam #' . $plan->id }}
                                </a>
                            </h5>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
</div>
@endsection
