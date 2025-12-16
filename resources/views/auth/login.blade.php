<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #0f2540 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }

        .logo {
            height: 60px;
            width: auto;
            object-fit: contain;
        }

        .logo-center {
            height: 75px;
            width: auto;
            object-fit: contain;
        }

        .login-header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1e3a5f;
            margin-bottom: 6px;
        }

        .login-header p {
            color: #64748b;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            font-size: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s ease;
            outline: none;
            background: #f8fafc;
        }

        .form-input:focus {
            border-color: #1e3a5f;
            background: white;
            box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.1);
        }

        .form-input.error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .error-message {
            color: #dc2626;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            cursor: pointer;
            accent-color: #1e3a5f;
        }

        .remember-me label {
            font-size: 14px;
            color: #475569;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            font-size: 15px;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8e 100%);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(30, 58, 95, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert-danger::before {
            content: "⚠";
            font-size: 18px;
        }

        .footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .footer p {
            font-size: 12px;
            color: #94a3b8;
        }

        .footer strong {
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo-container">
                <img src="{{ asset('images/govt-logo.png') }}" alt="Government Logo" class="logo" onerror="this.style.display='none'">
                <img src="{{ asset('images/epi-logo.png') }}" alt="EPI Logo" class="logo-center" onerror="this.style.display='none'">
                <img src="{{ asset('images/logo.png') }}" alt="TKF Logo" class="logo" onerror="this.style.display='none'">
            </div>
            <h1>Community Led Engagement</h1>
            <p>Sign in to access the admin dashboard</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input @error('email') error @enderror" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                    placeholder="admin@example.com"
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input @error('password') error @enderror" 
                    required
                    placeholder="••••••••"
                >
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Keep me signed in</label>
            </div>

            <button type="submit" class="btn-login">
                Sign In
            </button>
        </form>

        <div class="footer">
            <p>Powered by <strong>EPI & Tameer-e-Khalaq Foundation</strong></p>
        </div>
    </div>
</body>
</html>
