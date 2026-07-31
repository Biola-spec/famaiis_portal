@php
    $setting = App\Models\SiteSetting::find(1);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Portal login for {{ $setting->school_name ?? 'FAMA Islamic International School' }}">
    <meta name="author" content="FAMA">
    <link rel="icon" href="{{ (!empty($setting->logo)) ? url($setting->logo) : url('upload/no_image.jpg') }}">

    <title>{{ $setting->school_name ?? 'FAMA ISLAMIC INTERNATIONAL SCHOOL' }} - Portal Login</title>
  
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <!-- CSS Styling -->
    <style>
        :root {
            --primary: #0b1f3a; /* Deep Navy Blue */
            --primary-hover: #163e70;
            --primary-light: #e0f2fe; /* Light wash of sky blue */
            --accent: #38bdf8; /* Sky Blue */
            --accent-hover: #0ea5e9;
            --accent-light: #f0f9ff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --bg-page: #f8fafc;
            --white: #ffffff;
            --radius-lg: 24px;
            --radius-md: 12px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-page);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Glowing Blobs in Background */
        .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            z-index: 0;
            opacity: 0.45;
            pointer-events: none;
        }
        
        .bg-blob-1 {
            width: 500px;
            height: 500px;
            background-color: rgba(11, 31, 58, 0.08);
            top: -100px;
            left: -100px;
            animation: float-blob 20s infinite alternate;
        }

        .bg-blob-2 {
            width: 450px;
            height: 450px;
            background-color: rgba(56, 189, 248, 0.12);
            bottom: -50px;
            right: -50px;
            animation: float-blob 15s infinite alternate-reverse;
        }

        @keyframes float-blob {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(40px, 30px) scale(1.1); }
        }

        .portal-container {
            width: 100%;
            max-width: 960px;
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-lg), 0 1px 3px rgba(0, 0, 0, 0.01);
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            min-height: 600px;
            z-index: 10;
            position: relative;
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Brand Pane (Left Column) */
        .brand-pane {
            color: var(--white);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 50px 40px;
            background: linear-gradient(135deg, rgba(11, 31, 58, 0.93) 0%, rgba(5, 16, 33, 0.96) 100%)
                        @if(!empty($setting->login_image))
                        , url('{{ url($setting->login_image) }}')
                        @endif;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            overflow: hidden;
        }

        .brand-header, .brand-footer {
            position: relative;
            z-index: 2;
        }

        .brand-content-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            padding: 40px 35px;
            margin-top: auto;
            margin-bottom: auto;
            box-shadow: 0 12px 40px 0 rgba(0, 0, 0, 0.25);
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 2;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .school-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: var(--radius-md);
            padding: 8px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.15);
            margin-bottom: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .school-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.85rem;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: -0.5px;
            color: var(--white);
            margin-bottom: 12px;
        }

        .school-name span {
            color: var(--accent);
            display: block;
            font-size: 0.95rem;
            font-family: 'Outfit', sans-serif;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 8px;
            font-weight: 600;
        }

        .session-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(56, 189, 248, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.25);
            color: var(--accent-light);
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 600;
            margin-top: 15px;
            letter-spacing: 0.5px;
        }

        .badge-dot {
            width: 7px;
            height: 7px;
            background-color: var(--accent);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 10px var(--accent);
        }

        .support-info {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.55);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .support-info a {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
        }

        .support-info a:hover {
            color: var(--accent);
        }

        /* Form Pane (Right Column) */
        .form-pane {
            padding: 60px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--white);
            position: relative;
        }

        .pane-header {
            margin-bottom: 35px;
        }

        .greeting-msg {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--accent-hover);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .pane-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .pane-subtitle {
            font-size: 0.92rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        /* Mobile Logo Header (Hidden on Desktop) */
        .mobile-logo-header {
            display: none;
            text-align: center;
            margin-bottom: 30px;
        }

        .school-logo-mobile {
            width: 65px;
            height: 65px;
            object-fit: contain;
            border-radius: var(--radius-md);
            padding: 6px;
            background: rgba(11, 31, 58, 0.04);
            border: 1px solid rgba(11, 31, 58, 0.08);
            margin: 0 auto 12px auto;
        }

        .school-name-mobile {
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1.25;
        }

        .school-name-mobile span {
            display: block;
            font-size: 0.78rem;
            font-family: 'Outfit', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Form inputs */
        .input-group {
            margin-bottom: 22px;
            position: relative;
        }

        .input-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper svg.field-icon {
            position: absolute;
            left: 16px;
            width: 18px;
            height: 18px;
            color: var(--text-muted);
            pointer-events: none;
            transition: var(--transition);
        }

        .form-control {
            width: 100%;
            padding: 13px 16px 13px 48px;
            font-size: 0.95rem;
            font-family: inherit;
            color: var(--text-dark);
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: var(--radius-md);
            outline: none;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary);
            background-color: var(--white);
            box-shadow: 0 0 0 4px rgba(11, 31, 58, 0.08);
        }

        .form-control:focus + svg.field-icon {
            color: var(--primary);
        }

        /* Password toggle button */
        .password-toggle {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            border-radius: 50%;
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--primary);
            background-color: rgba(0, 0, 0, 0.05);
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        /* Options Row */
        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            font-size: 0.88rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            color: var(--text-muted);
            font-weight: 500;
        }

        .remember-me input {
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .forgot-link {
            color: var(--accent-hover);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .forgot-link:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 13px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: inherit;
            color: var(--white);
            background-color: var(--primary);
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(11, 31, 58, 0.15);
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.5px;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
            box-shadow: 0 10px 15px rgba(11, 31, 58, 0.25);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 4px 6px rgba(11, 31, 58, 0.15);
        }

        /* Form Footer styling */
        .form-footer {
            margin-top: auto;
            padding-top: 35px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .form-footer a:hover {
            color: var(--accent-hover);
        }

        /* Error/Session Container styling */
        .error-container {
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: var(--radius-md);
            padding: 15px;
            margin-bottom: 25px;
            color: #b91c1c;
            font-size: 0.85rem;
        }

        .error-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .error-header svg {
            width: 18px;
            height: 18px;
            color: #dc2626;
        }

        .error-list {
            padding-left: 20px;
            line-height: 1.5;
        }

        .success-container {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: var(--radius-md);
            padding: 15px;
            margin-bottom: 25px;
            color: #15803d;
            font-size: 0.85rem;
        }

        .success-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
        }

        .success-header svg {
            width: 18px;
            height: 18px;
            color: #16a34a;
        }

        /* Responsive Layout adjustments */
        @media (max-width: 950px) {
            .portal-container {
                grid-template-columns: 1fr;
                min-height: auto;
                max-width: 480px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
                border-radius: var(--radius-lg);
            }

            .brand-pane {
                display: none;
            }

            .mobile-logo-header {
                display: block;
            }

            .form-pane {
                padding: 45px 30px;
            }

            .form-footer {
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>

    <!-- Background decorative patterns -->
    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>

    <div class="portal-container">
        
        <!-- Left Pane: Branding Card and background school image -->
        <div class="brand-pane">
            <div class="brand-header">
                <!-- Keep empty or put dynamic links here if needed -->
            </div>

            <div class="brand-content-card">
                <img class="school-logo" src="{{ (!empty($setting->logo)) ? url($setting->logo) : url('upload/no_image.jpg') }}" alt="School Logo">
                <h1 class="school-name">
                    {{ $setting->school_name ?? 'FAMA ISLAMIC INTERNATIONAL SCHOOL' }}
                    <span>School Management Portal</span>
                </h1>
                
                <div class="session-badge">
                    <span class="badge-dot"></span>
                    Session: {{ $setting->current_session ?? '2025/2026' }}
                </div>
            </div>

            <div class="brand-footer">
                <div class="support-info">
                    <span>&copy; {{ date('Y') }} {{ $setting->school_name ?? 'FAMA' }}</span>
                    <span>&bull;</span>
                    <a href="mailto:support@fama.school">ICT Support Desk</a>
                </div>
            </div>
        </div>

        <!-- Right Pane: Login Form -->
        <div class="form-pane">

            <!-- Mobile Logo Header (Visible only on mobile/tablet) -->
            <div class="mobile-logo-header">
                <img class="school-logo-mobile" src="{{ (!empty($setting->logo)) ? url($setting->logo) : url('upload/no_image.jpg') }}" alt="School Logo">
                <h1 class="school-name-mobile">
                    {{ $setting->school_name ?? 'FAMA ISLAMIC INTERNATIONAL SCHOOL' }}
                    <span>School Management Portal</span>
                </h1>
            </div>
            
            <div class="pane-header">
                <div class="greeting-msg" id="greetingMsg">Assalamu Alaikum</div>
                <h2 class="pane-title">Portal Sign In</h2>
                <p class="pane-subtitle">Enter your registered email and password to access your dashboard.</p>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="error-container">
                    <div class="error-header">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <span>Login Attempt Failed</span>
                    </div>
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Session Status Alert -->
            @if (session('status'))
                <div class="success-container">
                    <div class="success-header">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Email Address -->
                <div class="input-group">
                    <label class="input-label" for="email">Email Address</label>
                    <div class="input-wrapper">
                        <svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <input class="form-control" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus autocomplete="username">
                    </div>
                </div>

                <!-- Password -->
                <div class="input-group">
                    <label class="input-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <input class="form-control" type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle Password Visibility">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <!-- Options: Remember Me & Forgot Password -->
                <div class="options-row">
                    <label class="remember-me" for="basic_checkbox_1">
                        <input type="checkbox" id="basic_checkbox_1" name="remember">
                        Keep me signed in
                    </label>
                    <a class="forgot-link" href="{{ route('password.request') }}">Recover Password?</a>
                </div>

                <!-- Sign In Button -->
                <button type="submit" class="btn-submit">
                    <span>Sign In</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>

            </form>

            <!-- Small clean footer inside form-pane (desktop/mobile unified view) -->
            <div class="form-footer">
                <span>&copy; {{ date('Y') }} {{ $setting->school_name ?? 'FAMA' }}</span>
                <span>&bull;</span>
                <a href="mailto:support@fama.school">ICT Support</a>
            </div>

        </div>

    </div>

    <!-- Password visibility toggle & Dynamic Greeting Script -->
    <script>
        // Time of Day Greeting
        const greetingMsg = document.getElementById('greetingMsg');
        const hour = new Date().getHours();
        let timeGreeting = "Assalamu Alaikum";
        if (hour < 12) {
            timeGreeting += ", Good Morning";
        } else if (hour < 17) {
            timeGreeting += ", Good Afternoon";
        } else {
            timeGreeting += ", Good Evening";
        }
        greetingMsg.innerText = timeGreeting;

        // Password Visibility Toggle
        const togglePasswordBtn = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        const eyeOpenPath = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        const eyeClosedPath = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';

        togglePasswordBtn.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            eyeIcon.innerHTML = type === 'text' ? eyeClosedPath : eyeOpenPath;
        });
    </script>

</body>
</html>
