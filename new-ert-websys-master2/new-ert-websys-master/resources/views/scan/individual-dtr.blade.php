@extends('layouts.app')

@section('title', 'Individual DTR')

@section('content')
    <div class="container mt-4">
        <h2>{{ $employee->name }} - Daily Attendance ({{ $monthName }} {{ $year }})</h2>

        <table class="table table-bordered mt-3 text-center">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>AM Time In</th>
                    <th>AM Time Out</th>
                    <th>PM Time In</th>
                    <th>PM Time Out</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($record['date'])->format('M d, Y (D)') }}</td>
                        <td>{{ $record['am_time_in'] ?? '-' }}</td>
                        <td>{{ $record['am_time_out'] ?? '-' }}</td>
                        <td>{{ $record['pm_time_in'] ?? '-' }}</td>
                        <td>{{ $record['pm_time_out'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('attendance.report', ['month' => $month]) }}" class="btn btn-secondary mt-3">← Back to Monthly Summary</a>
    </div>
@endsection
    