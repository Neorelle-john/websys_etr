@extends('layouts.app')

@section('title', 'Export Individual Attendance')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
<style>
    * {
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background-color: #f5f5f5;
    }

    h1, h2, h3 {
        color: #0A28D8;
    }

    .export-container {
        max-width: 600px;
        margin: 50px auto;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .export-container img {
        width: 100px;
        margin-bottom: 20px;
    }

    .export-container p {
        color: #6b7280;
        margin-bottom: 30px;
    }

    label {
        font-weight: 600;
        display: block;
        margin-bottom: 6px;
        text-align: left;
    }

    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    button {
        background-color: #0A28D8;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #0731a7;
    }
</style>
@endpush

@section('content')
<div class="export-container">

    <h3 class="text-xl font-semibold mb-4">Export Individual Attendance</h3> <br><br>

    <!-- Export Form -->
    <form action="{{ route('export.individual') }}" method="GET">
        @csrf

        <!-- Employee Selection -->
        <div class="form-group text-left">
            <label for="employee_id">Select Employee:</label>
            <select name="employee_id" id="employee_id" required>
                <option value="" disabled selected>-- Select Employee --</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Month Selection -->
        <div class="form-group text-left">
            <label for="month">Select Month:</label>
            <select name="month" id="month" required>
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endfor
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit">Export Excel</button>
    </form>
</div>
@endsection
