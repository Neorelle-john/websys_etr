@extends('layouts.app')

@section('title', 'Monthly Attendance Summary')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
<style>
    * {
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background-color: #f8f9fa;
    }

    .container {
        padding: 30px;
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }

    h1 {
        color: #0A28D8;
        font-size: 28px;
        font-weight: 600;
        text-align: center;
        margin-bottom: 30px;
    }

    .alert {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        padding: 15px 20px;
        color: #155724;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 25px;
    }

    a.button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #0A28D8;
        color: #fff;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        margin-bottom: 20px;
        transition: background-color 0.3s ease;
    }

    a.button:hover {
        background-color: #0731a7;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    table thead {
        background-color: #0A28D8;
        color: #fff;
    }

    table th,
    table td {
        padding: 12px 15px;
        border: 1px solid #dee2e6;
        text-align: center;
        font-size: 14px;
    }

    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .action-buttons a,
    .action-buttons form {
        display: inline-block;
    }

    .btn,
    .action-buttons form button {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
    }
    .action-buttons {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.action-buttons a,
.action-buttons form button {
    width: 100px;
    text-align: center;
    padding: 8px 0;
    font-size: 14px;
    border-radius: 4px;
    font-weight: normal;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.action-buttons a {
    background-color: #0A28D8;
    color: white;
    text-decoration: none;
}

.action-buttons a:hover {
    background-color: #0731a7;
}

.action-buttons form {
    margin: 0;
}

.action-buttons form button {
    background-color: #dc3545;
    color: white;
}

.action-buttons form button:hover {
    background-color: #c82333;
}

    .btn {
        background-color: #0A28D8;
        color: white;
    }

    .btn:hover {
        background-color: #0731a7;
    }

    .btn-download {
        background-color: #28a745;
        white-space: nowrap;
    }

    .btn-download:hover {
        background-color: #218838;
    }

    .action-buttons form button {
        background-color: #dc3545;
        color: white;
    }

    .action-buttons form button:hover {
        background-color: #c82333;
    }

    #success-alert {
        opacity: 1;
        transition: opacity 1s ease;
    }

    #success-alert.fade-out {
        opacity: 0;
    }

    .pagination {
        text-align: center;
        margin-top: 30px;
    }

    .pagination-btn {
        display: inline-block;
        margin: 0 5px;
        padding: 8px 16px;
        background-color: #0A28D8;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    .pagination-btn:hover {
        background-color: #0731a7;
    }

    .pagination-btn.disabled {
        background-color: #ccc;
        color: #666;
        cursor: not-allowed;
        pointer-events: none;
    }

    .pagination-btn.active {
        background-color: #FFDA27;
        color: #000;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h1>Employee List</h1>

    @if (session('success'))
        <div class="alert alert-success" id="success-alert">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('employees.create') }}" class="button">+ Add Employee</a>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>ID Number</th>
                <th>Class Role</th>
                <th>College</th>
                <th colspan="3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $employee)
                <tr>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->id_number }}</td>
                    <td>{{ $employee->classification }}</td>
                    <td>{{ $employee->college }}</td>
                    <td class="action-buttons">
                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn">Edit</a>
                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                    <td><a href="{{ route('employees.show', $employee->id) }}" class="btn">Show</a></td>
                    <td><a href="{{ route('employees.downloadQrCode', $employee->id) }}" class="btn btn-download">Download QR Code</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination">
        @if ($employees->onFirstPage())
            <span class="pagination-btn disabled">First</span>
            <span class="pagination-btn disabled">Previous</span>
        @else
            <a href="{{ $employees->url(1) }}" class="pagination-btn">First</a>
            <a href="{{ $employees->previousPageUrl() }}" class="pagination-btn">Previous</a>
        @endif

        @foreach ($employees->getUrlRange(max(1, $employees->currentPage() - 2), min($employees->lastPage(), $employees->currentPage() + 2)) as $page => $url)
            <a href="{{ $url }}" class="pagination-btn {{ $employees->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        @if ($employees->hasMorePages())
            <a href="{{ $employees->nextPageUrl() }}" class="pagination-btn">Next</a>
            <a href="{{ $employees->url($employees->lastPage()) }}" class="pagination-btn">Last</a>
        @else
            <span class="pagination-btn disabled">Next</span>
            <span class="pagination-btn disabled">Last</span>
        @endif
    </div>
</div>

<script>
    setTimeout(function () {
        var alert = document.getElementById('success-alert');
        if (alert) {
            alert.classList.add('fade-out');
            setTimeout(function () {
                alert.style.display = 'none';
            }, 1000);
        }
    }, 3000);
</script>
@endsection
