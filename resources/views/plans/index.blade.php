@extends('layouts.app')

@section('title', 'Stravovacie plány')

@section('content')
<div class="container my-5">
    <h1 class="fw-bold text-center mb-5">Stravovacie plány</h1>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4 p-md-5">
            <h3 class="fw-bold text-center mb-4">Nový stravovací plán</h3>
            
            <div id="vue-app">
                <form action="" method="POST">
                    @csrf
            
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="date_from" class="form-label fw-medium">Dátum začiatku</label>
                            <input type="date" 
                                   name="date_from" 
                                   id="date_from" 
                                   class="form-control" 
                                   v-model="dateFrom"
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label for="period" class="form-label fw-medium">Obdobie jedálnička</label>
                            <select id="period" 
                                    name="period" 
                                    class="form-select" 
                                    v-model="period"
                                    required>
                                <option value="" disabled selected>Vyber obdobie</option>
                                <option value="2">2 dni</option>
                                <option value="7">7 dní</option>
                                <option value="14">14 dní</option>
                                <option value="30">30 dní</option>
                            </select>
                        </div>
                    </div>
            
                    <!-- Vue komponent -->
                    <meal-plan 
                        :start-date="dateFrom"
                        :period="parseInt(period)"
                        :recipes='@json($recipes)'>
                </meal-plan>
            
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            Vytvoriť plán
                        </button>
                    </div>
                </form>
            </div>
            
            
        </div>
    </div>
</div>

@endsection