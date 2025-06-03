<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
              * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Poppins', sans-serif;
            }

            body, html {
                height: 100%;
                width: 100%;
            }

            .container {
                display: flex;
                height: 100vh;
            }

            /* Left side = Form (now dark blue) */
            .left-side {
                flex: 1;
                background-color: #0A28D8; /* Deep Blue */
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .login-form {
                width: 80%;
                max-width: 400px;
                color: #fff;
            }

            .login-form h2 {
                font-size: 28px;
                margin-bottom: 10px;
            }

            .login-form p {
                font-size: 14px;
                margin-bottom: 30px;
                color: #d1d1d1;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                font-size: 14px;
                margin-bottom: 5px;
            }

            .form-group input {
                width: 100%;
                padding: 12px;
                border: 1px solid #ccc;
                border-radius: 8px;
                background: transparent;
                color: #fff;
            }

            .form-group input::placeholder {
                color: #ccc;
            }

            .error {
                font-size: 12px;
                color:#FF3B30;
                margin-top: 5px;
                display: block;
            }

            .success {
                font-size: 14px;
                color: #4CAF50;
                margin-bottom: 15px;
            }

            .login-btn {
                width: 100%;
                padding: 12px;
                
                background-color: #FFDA27;
                border: none;
                border-radius: 8px;
                font-weight: bold;
                color: #0A28D8;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .login-btn:hover {
                background-color: #e6c122;
            }

            /* Right side = Logo & Tagline (now white) */
            .right-side {
                flex: 1;
                background-color: #ffffff;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                position: relative;
            }

            .right-side .logo {
                width: 200px;
                margin-bottom: 20px;
            }

            .right-side h2 {
                color: #0A28D8;
                font-size: 24px;
                max-width: 400px;
                margin-bottom: 10px;
                text-align: center;
            }

            .right-side p {
                color: #999;
                font-size: 14px;
                max-width: 300px;
                text-align: center;
            }

            /* Optional: Nice decorative squares (if you want same as your image) */
            .right-side::before,
            .right-side::after {
                content: "";
                position: absolute;
                width: 20px;
                height: 20px;
                background: #0A28D8;
            }

            .right-side::before {
                top: 20px;
                right: 20px;
            }

            .right-side::after {
                bottom: 20px;
                left: 20px;
            }


    </style>
</head>
<body>
<div class="container">
    <div class="left-side">
        <form action="/login" method="post" class="login-form">
            @csrf
            <h2>Welcome Admin</h2>
            <p>Enter your email and password to login</p>

            @if(session('success'))
                <p class="success">{{session('success')}}</p>
            @endif

            <div class="form-group">
                <label for="email">Email<span>*</span></label>
                <input type="email" name="email" placeholder="Enter your email" value="{{old('email')}}">
                @error('email')
                    <span class="error">{{$message}}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password<span>*</span></label>
                <input type="password" name="password" placeholder="Enter your password">
                @error('password')
                    <span class="error">{{$message}}</span>
                @enderror
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>

    <div class="right-side">
        <img src="/images/psulogo.png" alt="Logo" class="logo">
        <h2>Pangasinan State University - Urdaneta City Campus</h2>
        <p>Region's Premier University of Choice</p>
    </div>
</div>
</body>
</html>