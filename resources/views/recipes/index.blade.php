@extends('layouts.app')

@section('title', 'Recepty')

@section('content')
@include('recipes.new_recipe_modal')

<recipes :recipes='@json($recipes ?? [])'></recipes>
@endsection
