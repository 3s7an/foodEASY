@extends('layouts.app')

@section('title', 'Stravovacie plány')

@section('content')
<div class="container my-5">
    <h1 class="fw-bold text-center mb-5">Stravovacie plány</h1>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4 p-md-5">
            <h3 class="fw-bold text-center mb-4">Nový stravovací plán</h3>
            
            <form action="" method="POST">
                @csrf

  

                <!-- Dátum a obdobie -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="date_from" class="form-label fw-medium">Dátum začiatku</label>
                        <input type="date" 
                               name="date_from" 
                               id="date_from" 
                               class="form-control" 
                               value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                               required>
                        @error('date_from')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="period" class="form-label fw-medium">Obdobie jedálnička</label>
                        <select id="period" 
                                name="period" 
                                class="form-select" 
                                required>
                            <option value="" disabled selected>Vyber obdobie</option>
                            <option value="2">2 dni</option>
                            <option value="7">7 dní</option>
                            <option value="14">14 dní</option>
                            <option value="30">30 dní</option>
                        </select>
                        @error('period')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Výber receptov -->
                <div class="mb-4">
                    <label for="recipes" class="form-label fw-medium mb-3">Vyber si jedlá</label>
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3 col-lg-2">
                            <span class="text-muted fs-5">12.03.2024</span>
                        </div>
                        <div class="col-md-9 col-lg-10">
                            <select id="recipes" 
                                    name="recipes[]" 
                                    class="form-select" >
                                @foreach ($recipes as $recipe)
                                    <option value="{{ $recipe->id }}">{{ $recipe->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @error('recipes')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tlačidlo -->
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" 
                            class="btn btn-primary btn-lg px-4">
                        Vytvoriť plán
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection