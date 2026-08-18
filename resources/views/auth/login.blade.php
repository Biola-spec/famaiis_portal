@php
    $schoolSetting = \Illuminate\Support\Facades\Schema::hasTable('school_settings')
        ? App\Models\SchoolSetting::first()
        : null;
    $legacySetting = App\Models\SiteSetting::find(1);
    $setting = $schoolSetting ?: $legacySetting;
    $schoolName = $setting->school_name ?? config('app.name', 'FAMA ISLAMIC INTERNATIONAL SCHOOL');
    $schoolLogoPath = $setting->logo ?? $setting->logo_path ?? null;
    $schoolLogo = !empty($schoolLogoPath) ? url($schoolLogoPath) : url('upload/no_image.jpg');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal login for {{ $schoolName }}">
    <title>{{ $schoolName }} - Portal Login</title>
    <style>
        {!! file_get_contents(base_path('login-forms/forms/corporate/style.css')) !!}

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #dbeafe 100%);
        }

        .login-card {
            padding: 36px;
        }

        .company-logo {
            display: flex;
            justify-content: center;
        }

        .school-logo {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            object-fit: contain;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.18);
        }

        .login-header h2 {
            font-size: 1.55rem;
            line-height: 1.25;
        }

        .login-header p {
            max-width: 300px;
            margin: 0 auto;
        }

        .school-label {
            color: #2563eb;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .portal-form .input-wrapper input {
            padding-right: 16px;
        }

        .portal-form .password-wrapper input {
            padding-right: 52px;
        }

        .portal-form .password-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            padding: 8px;
        }

        .portal-form .password-toggle svg {
            width: 18px;
            height: 18px;
        }

        .server-alert {
            border-radius: 8px;
            padding: 12px 14px;
            margin: -10px 0 24px;
            font-size: 13px;
            line-height: 1.45;
        }

        .server-alert.error {
            color: #991b1b;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .server-alert.success {
            color: #166534;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .server-alert ul {
            margin: 6px 0 0 18px;
            padding: 0;
        }

        .form-footer {
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 28px;
            padding-top: 18px;
            font-size: 12px;
        }

        .form-footer a {
            color: #64748b;
            text-decoration: none;
        }

        .form-footer a:hover {
            color: #2563eb;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 28px 22px;
            }
        }
    </style>
</head>
<body>
    <main class="login-container">
        <section class="login-card" aria-labelledby="login-title">
            <header class="login-header">
                <div class="company-logo">
                    <img class="school-logo" src="{{ $schoolLogo }}" alt="{{ $schoolName }} logo">
                </div>
                <div class="school-label">{{ $schoolName }}</div>
                <h1 id="login-title">Welcome Back</h1>
                <p>Sign in to access your school workspace.</p>
            </header>

            @if ($errors->any())
                <div class="server-alert error" role="alert">
                    <strong>Unable to sign in</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="server-alert success" role="status">{{ session('status') }}</div>
            @endif

            <form class="login-form portal-form" method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" aria-label="Email address">
                        <label for="email">Email Address</label>
                        <span class="input-border"></span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper password-wrapper">
                        <input type="password" id="password" name="password" required autocomplete="current-password" aria-label="Password">
                        <label for="password">Password</label>
                        <button type="button" class="password-toggle" id="passwordToggle" aria-label="Show password" aria-pressed="false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                        <span class="input-border"></span>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-wrapper" for="remember">
                        <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkbox-label">
                            <span class="checkbox-custom"></span>
                            Keep me signed in
                        </span>
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-password">Reset password</a>
                </div>

                <button type="submit" class="login-btn" id="loginButton">
                    <span class="btn-text">Sign In</span>
                    <span class="btn-loader" aria-hidden="true"></span>
                </button>
            </form>

            <footer class="form-footer">
                <span>&copy; {{ date('Y') }} {{ $schoolName }}</span>
                <span aria-hidden="true">&bull;</span>
                <a href="mailto:support@fama.school">ICT Support</a>
            </footer>
        </section>
    </main>

    <script>
        const passwordField = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');

        passwordToggle.addEventListener('click', function () {
            const showing = passwordField.type === 'text';
            passwordField.type = showing ? 'password' : 'text';
            passwordToggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            passwordToggle.setAttribute('aria-pressed', String(!showing));
        });

        loginForm.addEventListener('submit', function () {
            loginButton.classList.add('loading');
            loginButton.setAttribute('aria-disabled', 'true');
        });
    </script>
</body>
</html>
