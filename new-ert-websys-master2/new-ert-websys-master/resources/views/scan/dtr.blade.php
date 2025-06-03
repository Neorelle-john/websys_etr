@extends('layouts.app')

@section('title', 'Monthly Attendance Summary')

@section('content')

<h2>Monthly Report on Undertime and Absences</h2>

<p class="header" style="text-align: center;">
    For the Month of:
    {{
        \Carbon\Carbon::createFromDate(
            now()->year,
            request('month',  now()->month),
            1
        )->format('F Y')
    }}
</p>

<form action="{{ route('attendance.report') }}" method="GET">
    <label for="role">Role:</label>
    <select name="role" id="role">
        <option value="">All</option>
        <option value="Instructional" {{ request('role') == 'Instructional' ? 'selected' : '' }}>Instructional</option>
        <option value="Non-Instructional" {{ request('role') == 'Non-Instructional' ? 'selected' : '' }}>Non-Instructional</option>
    </select>

    <label for="month">Month:</label>
    <select name="month" id="month">
        @foreach(range(1,12) as $m)
            <option value="{{ $m }}" {{ request('month', now()->month)==$m ? 'selected' : '' }}>
                {{ \Carbon\Carbon::createFromDate(now()->year, $m, 1)->format('F') }}
            </option>
        @endforeach
    </select>
    <button type="submit">Filter</button>
</form>

<!-- Export Buttons -->
<div style="margin: 20px 0;">
    <form action="{{ route('attendance.excel') }}" method="GET" style="display: inline;">
        <input type="hidden" name="month" value="{{ request('month', now()->month) }}">
        <button type="submit" class="pagination-btn btn-download">Export Excel</button>
    </form>

  <a href="{{ route('export.form') }}" class="pagination-btn btn-download" style="margin-left: 10px; font-weight: bold;">
    Individual DTR
</a>

</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Employee Name</th>
            <th>Undertime</th>
            <th>Absences</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $i => $r)
            <tr>
                <td>{{ ($records->currentPage() - 1) * $records->perPage() + $i + 1 }}</td>
                <td>{{ $r['name'] }}</td>
                <td>{{ $r['undertime'] ?: '' }}</td>
                <td>{{ $r['absences'] ?? '' }}</td>

            </tr>
        @endforeach
    </tbody>
</table>

<div class="pagination">
    @if ($records->onFirstPage())
        <span class="pagination-btn disabled">First</span>
        <span class="pagination-btn disabled">Previous</span>
    @else
        <a href="{{ $records->url(1) }}" class="pagination-btn">First</a>
        <a href="{{ $records->previousPageUrl() }}" class="pagination-btn">Previous</a>
    @endif

    @foreach ($records->getUrlRange(max(1, $records->currentPage() - 2), min($records->lastPage(), $records->currentPage() + 2)) as $page => $url)
        <a href="{{ $url }}" class="pagination-btn {{ $records->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
    @endforeach

    @if ($records->hasMorePages())
        <a href="{{ $records->nextPageUrl() }}" class="pagination-btn">Next</a>
        <a href="{{ $records->url($records->lastPage()) }}" class="pagination-btn">Last</a>
    @else
        <span class="pagination-btn disabled">Next</span>
        <span class="pagination-btn disabled">Last</span>
    @endif
</div>

@endsection
