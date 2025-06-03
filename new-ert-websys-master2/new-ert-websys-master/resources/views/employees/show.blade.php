@extends('layouts.app')

@section('title', 'Employee Details')

@section('content')
    <div class="wrapper">
        <a href="{{ route('employees.index') }}" class="back-btn">Back to List</a>
        <div class="container">
            <div class="card-layout"
                style="display:flex; flex-wrap: wrap; gap: 30px; align-items: center; justify-content: space-between;">
                <div class="qr-box" style="flex: 1 1 200px; text-align: center;">
                    {!! $qr !!}
                </div>
                <div class="info-box" style="flex: 2 1 300px;">
                    @if ($employee->picture)
                        <div class="employee-img" style="margin-top: 20px;">
                            <img src="{{ asset('images/' . $employee->picture) }}" alt="Employee Picture"
                                style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #ccc; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" />
                        </div>
                    @endif
                    <h1 style="color: #0A28D8; font-size: 26px; margin-bottom: 15px;">{{ $employee->name }}</h1>
                    <p style="margin: 8px 0; font-size: 16px;"><strong>ID:</strong> {{ $employee->id_number }}</p>
                    <p style="margin: 8px 0; font-size: 16px;"><strong>Email:</strong> {{ $employee->classification }}</p>
                    <p style="margin: 8px 0; font-size: 16px;"><strong>College:</strong> {{ $employee->college }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection