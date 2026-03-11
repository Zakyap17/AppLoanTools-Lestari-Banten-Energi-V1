<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TEMS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 1200px;
            height: 700px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* Left Side - Background Image */
        .login-left {
            background: url("{{ asset('images/login-bg.png') }}") no-repeat center center;
            background-size: cover;
            position: relative;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.7) 0%, rgba(15, 23, 42, 0.85) 100%);
            z-index: 0;
        }

        .login-left>* {
            position: relative;
            z-index: 1;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 10px;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-name {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .welcome-text {
            margin-top: auto;
        }

        .welcome-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .welcome-text p {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.6;
            max-width: 400px;
        }

        /* Right Side - Login Form */
        .login-right {
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fafafa;
        }

        .form-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .company-logo {
            width: 90px;
            height: 90px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .form-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 8px;
        }

        .form-header p {
            color: #718096;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            font-size: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #a0aec0;
            cursor: pointer;
            padding: 4px;
            font-size: 18px;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: #3b82f6;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4a5568;
        }

        .remember-me input {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .forgot-password {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-password:hover {
            color: #2563eb;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: #1a202c;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 20px;
        }

        .btn-login:hover {
            background: #2d3748;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 24px;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background: #fee;
            color: #c53030;
        }

        .footer-text {
            text-align: center;
            font-size: 13px;
            color: #a0aec0;
            margin-top: 32px;
        }

        @media (max-width: 968px) {
            .login-container {
                grid-template-columns: 1fr;
                height: auto;
            }

            .login-left {
                min-height: 300px;
                padding: 40px;
            }

            .welcome-text h1 {
                font-size: 36px;
            }

            .login-right {
                padding: 40px 30px;
            }

            .form-header h2 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Left Side - Branding & Background -->
        <div class="login-left">
            <div class="brand-section">
                <div class="brand-logo">
                    <img src="{{ asset('assets/images/logo-new.png') }}" alt="LBE">
                </div>
                <div class="brand-name">PT LBE</div>
            </div>

            <div class="welcome-text">
                <h1><span id="dynamic-greeting">Start Your Day</span></h1>
                <p>Every great achievement begins with the decision to try. Let's make today count and achieve
                    extraordinary results together.</p>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="form-header">
                <div class="company-logo">
                    <img src="{{ asset('assets/images/logo-new.png') }}" alt="LBE">
                </div>
                <h2>Welcome Back</h2>
                <p>Enter your username and password to access</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter your username"
                        value="{{ old('username') }}" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" class="form-control"
                            placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    Sign In
                </button>
            </form>

            <div class="footer-text">
                &copy; {{ date('Y') }} PT LBE. All rights reserved.
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        if (togglePassword && password) {
            togglePassword.addEventListener('click', function () {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);

                const icon = this.querySelector('i');
                if (type === 'text') {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        }

        // Dynamic Greeting Logic
        function updateGreeting() {
            const now = new Date();
            const hour = now.getHours();
            let greeting = 'Start Your Day';
            
            if (hour >= 5 && hour < 12) {
                greeting = 'Good Morning';
            } else if (hour >= 12 && hour < 17) {
                greeting = 'Good Afternoon';
            } else if (hour >= 17 && hour < 21) {
                greeting = 'Good Evening';
            } else {
                greeting = 'Good Night';
            }
            
            const greetingEl = document.getElementById('dynamic-greeting');
            if(greetingEl) greetingEl.innerText = greeting;
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateGreeting();
            setInterval(updateGreeting, 60000);
        });
    </script>
</body>

</html>