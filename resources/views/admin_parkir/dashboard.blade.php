@extends('layouts.parkir')

@section('title', 'Dashboard Parkir')
@section('heading', 'Dashboard Operasional Parkir')
@section('subheading', 'Ringkasan aktivitas parkir hari ini')

@section('content')
    <div class="card">
        <p>Halo, {{ auth()->user()->name }}</p>
    </div>
@endsection