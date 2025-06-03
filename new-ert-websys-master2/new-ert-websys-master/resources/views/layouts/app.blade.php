<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'PSU System')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        /* Fixed Sidebar */
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

        .sidebar form {
            display: none;
        }

        .logout-link {
            text-align: center;
            color: #ffdddd;
            font-weight: bold;
            text-decoration: none;
            margin-top: 20px;
        }

        .logout-link:hover {
            text-decoration: underline;
            color: white;
        }

        /* Main content */
        .main {
            margin-left: 250px;
            padding: 40px 20px;
            width: calc(100% - 250px);
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
        }

        .container {
            width: 100%;
            max-width: 1400px;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }

        .logo-container img {
            width: 150px;
            margin-bottom: 15px;
        }

        .logo-container h2 {
            color: #0A28D8;
            font-size: 22px;
            text-align: center;
        }

        .logo-container p {
            color: #999;
            font-size: 14px;
            text-align: center;
        }

        .back-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #0A28D8;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #FFDA27;
            color: #0A28D8;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
            color: #0A28D8;
        }

        form {
            margin-bottom: 20px;
            text-align: center;
        }

        form label {
            margin-right: 10px;
            font-weight: 500;
        }

        form select {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-right: 15px;
            font-size: 14px;
        }

        form button {
            padding: 10px 20px;
            background-color: #0A28D8;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #FFDA27;
            color: #0A28D8;
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
            border: 1px solid #ddd;
            text-align: center;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination-btn {
            display: inline-block;
            padding: 8px 14px;
            margin: 0 5px;
            background-color: #0A28D8;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .pagination-btn:hover:not(.disabled) {
            background-color: #FFDA27;
            color: #0A28D8;
        }

        .pagination-btn.disabled {
            background-color: #ccc;
            cursor: default;
        }

        .pagination-btn.active {
            background-color: #FFDA27;
            font-weight: bold;
            color: #0A28D8;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <img src="/images/psulogo.png" alt="PSU Logo" />
             <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('employees.index') }}">List of Users</a>
            <a href="{{ route('attendance.report') }}">DTR</a>
           
        </div>
        <a href="{{ route('logout') }}" class="logout-link"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
        </form>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="container">
            <div class="logo-container">
                <img src="/images/psulogo.png" alt="PSU Logo" />
                <h2>Pangasinan State University - Urdaneta City Campus</h2>
                <p>Region's Premier University of Choice</p>
            </div>

            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>

</html>
