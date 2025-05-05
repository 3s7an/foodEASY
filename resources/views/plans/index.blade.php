@extends('layouts.app')

@section('title', 'Stravovacie plány')

@section('content')
<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="fw-bold text-center">Stravovacie plány</h1>
        <button type="button" class="btn btn-secondary" @click="openNewPlanModal">
            Nový stravovací plán
            <i class="fas fa-plus fa-xs ms-1"></i>
        </button>
    </div>

    @include('plans.new_plan_modal', [
        'recipes' => $recipes
    ])

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-4">
        @foreach ($plans as $plan)
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <div class="bg-warning text-white d-flex align-items-center justify-content-center" style="height: 60px;">
                        <i class="fas fa-clipboard-list fa-lg me-2"></i>
                        <span class="fw-semibold">Stravovací plán</span>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-2 text-dark">{{ $plan->name ?? 'Plán #' . $plan->id }}</h5>

                        <ul class="list-unstyled mb-3 text-muted small">
                            <li><i class="fas fa-calendar-alt me-1"></i> Vytvorený: {{ $plan->created_at->format('d.m.Y') }}</li>
                            <li><i class="fas fa-utensils me-1"></i> Unikátnych receptov: {{ $plan->recipes->unique('id')->count() ?? '0' }}</li>
                            <li><i class="fas fa-clock me-1"></i> Počet dní: {{ $plan->duration ?? 'neuvedené' }}</li>
                        </ul>
 
                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <a href="{{ route('plans.view', $plan->id) }}" class="btn btn-sm btn-outline-secondary" title="Zobraziť detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('plans.edit', $plan->id) }}" class="btn btn-sm btn-outline-warning" title="Upraviť">
                                <i class="fas fa-edit"></i>
                            </a>
                         
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>


@endsection
