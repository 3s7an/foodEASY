@extends('layouts.app')

@section('title', 'Nový stravovací plán')

@section('content')
    <div class="container">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4 p-md-5">
                <h3 class="fw-bold text-center mb-4">Nastavenie jedálničku</h3>
                    <form action=" {{ route('plans.store') }}" method="POST">
                        @csrf

                        <meal-plan :start-date="dateFrom" :period="parseInt(period)"
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
@endsection
