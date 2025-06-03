<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Attendance Export</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="3" style="text-align: center; font-size: 16px;">
                    For {{ $monthYear }}
                </th>
            </tr>
            <tr>
                <th>Name</th>
                <th>Undertime</th>
                <th>Absences</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record['name'] }}</td>
                    <td>{{ $record['undertime'] }}</td>
                    <td>{{ $record['absences'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
