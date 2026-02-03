<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        @if (session('session_expired'))
            <div class="alert alert-danger">
                Your session has expired. Please try again.
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

    <script>
        // Refresh CSRF token every 10 minutes to prevent expiration
        const CSRF_REFRESH_INTERVAL = 10 * 60 * 1000; // 10 minutes

        async function refreshCsrfToken() {
            try {
                const response = await fetch('{{ url("/sanctum/csrf-cookie") }}', {
                    method: 'GET',
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    // Get the new token from the cookie and update the form
                    const cookies = document.cookie.split(';');
                    for (let cookie of cookies) {
                        const [name, value] = cookie.trim().split('=');
                        if (name === 'XSRF-TOKEN') {
                            const token = decodeURIComponent(value);
                            // Update the hidden CSRF input in the form
                            const csrfInput = document.querySelector('input[name="_token"]');
                            if (csrfInput) {
                                csrfInput.value = token;
                            }
                            // Update the meta tag
                            const metaTag = document.querySelector('meta[name="csrf-token"]');
                            if (metaTag) {
                                metaTag.content = token;
                            }
                            console.log('CSRF token refreshed');
                            break;
                        }
                    }
                }
            } catch (error) {
                console.warn('Failed to refresh CSRF token:', error);
            }
        }

        // Refresh token periodically
        setInterval(refreshCsrfToken, CSRF_REFRESH_INTERVAL);

        // Handle form submission with 419 error recovery
        document.querySelector('form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'text/html,application/xhtml+xml'
                    }
                });

                if (response.status === 419) {
                    // CSRF token expired - reload page with message
                    window.location.href = '{{ route("login") }}?session_expired=1';
                    return;
                }

                // For successful response or other errors, follow the redirect
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    // Parse HTML response and update page (for validation errors)
                    const html = await response.text();
                    document.documentElement.innerHTML = html;
                }
            } catch (error) {
                // Network error - submit form normally as fallback
                form.submit();
            }
        });

        // Check URL for session_expired parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('session_expired') === '1') {
            // Clean up URL
            window.history.replaceState({}, '', '{{ route("login") }}');
        }
    </script>
</body>
</html>
