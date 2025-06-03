@extends('layouts.app')

@section('title', 'Employee Dashboard')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
<style>
    * {
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        margin: 0;
        background-color: #f5f5f5;
    }

    .layout {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 250px;
            background-color: #0A28D8;
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
    }
    .sidebar img {
            width: 100px;
            margin: 0 auto 30px;
            display: block;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin: 6px 0;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #FFDA27;
            color: #0A28D8;
        }

    .sidebar h2 {
        font-size: 22px;
        margin-bottom: 30px;
        text-align: center;
    }



    .main-content {
        flex-grow: 1;
        padding: 40px;
    }

    h1 {
        color: #0A28D8;
        font-size: 28px;
        margin-bottom: 30px;
    }

    .form-inline {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        flex-wrap: wrap;
        align-items: center;
    }

    .form-inline label {
        font-weight: 600;
    }

    .form-inline select,
    .form-inline button {
        padding: 10px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    .btn {
        background-color: #0A28D8;
        color: white;
        font-weight: 600;
        border: none;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #FFDA27;
    }

    .row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .card-box {
        flex: 1 1 45%;
        background-color: #2e47d3;
        border: 1.5px solid #FFDA27;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }

    .card-box h5 {
        color: #FFFFFF;
        font-size: 18px;
        margin-bottom: 10px;
    }

    .card-box p {
        font-size: 28px;
        font-weight: bold;
        color: #FFFFFF;
    }

    h4 {
        color: #0A28D8;
        font-size: 20px;
        text-align: center;
        margin-bottom: 20px;
    }

    canvas {
        background: white;
        border-radius: 8px;
        padding: 20px;
        width: 100%;
        max-width: 100%;
    }

    @media (max-width: 768px) {
        .layout {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="layout">
    <div class="main-content">
        <h1>Employee Dashboard</h1>

        <form method="GET" action="{{ url()->current() }}" class="form-inline">
            <label for="month">Select Month:</label>
            <select name="month" id="month">
                @foreach(range(1, 12) as $monthNumber)
                    <option value="{{ $monthNumber }}" {{ $selectedMonth == $monthNumber ? 'selected' : '' }}>
                        {{ \DateTime::createFromFormat('!m', $monthNumber)->format('F') }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn">Filter</button>
        </form>

        <div class="row">
            <div class="card-box">
                <h5>Instructional Employees</h5>
                <p>{{ $instructionalCount }}</p>
            </div>
            <div class="card-box">
                <h5>Non-Instructional Employees</h5>
                <p>{{ $nonInstructionalCount }}</p>
            </div>
        </div>

        <h4>Top 5 Employees with Highest Undertime ({{ \DateTime::createFromFormat('!m', $selectedMonth)->format('F') }})</h4>
        <canvas id="undertimeChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('undertimeChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($topUndertime, 'name')) !!},
            datasets: [{
                label: 'Undertime (in minutes)',
                data: {!! json_encode(array_column($topUndertime, 'minutes')) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.7)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Minutes'
                    }
                }
            }
        }
    });
</script>
@endsection
