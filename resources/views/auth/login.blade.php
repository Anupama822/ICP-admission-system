<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Informatics College Pokhara Admission Portal</title>

    <!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite('resources/sass/app.scss')

    <style>
        :root {
            --white: #fff;
            --highlight-heading-color: #9F0D1A;
            --general-color: #676767;
            --heading-color: #484848;
            --btn-color: #8D2229;
            --highlight-color: #971F20;
            --background-color: #FBFBFB;
            --hover-color: #f53d3d;
            --dark-panel: #232323;
        }

        * {
            accent-color: var(--highlight-color);
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Manrope', sans-serif;
            background-color: var(--background-color);
            color: var(--general-color);
        }

        ::selection {
            background-color: var(--highlight-color);
            color: var(--white);
        }

        .page-wrapper {
            min-height: 100vh;
        }

        /* ---------- Left banner panel ---------- */
        .brand-panel {
            position: relative;
            min-height: 100vh;
            background-color: var(--dark-panel);
            overflow: hidden;
            padding: 3rem;
        }

        .brand-panel__bg {
            position: absolute;
            inset: 0;
            z-index: 0;
        }

        .brand-panel__bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            opacity: 0.4;
        }

        .brand-panel__overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, var(--dark-panel) 0%, rgba(35, 35, 35, 0.7) 55%, rgba(141, 34, 41, 0.6) 100%);
        }

        .brand-panel__content {
            position: relative;
            z-index: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-logo-box {
            display: inline-flex;
            padding: 0.5rem;
            background-color: var(--white);
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .brand-logo-box img {
            height: 3rem;
            width: auto;
            object-fit: contain;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.9rem;
            border-radius: 999px;
            background-color: var(--btn-color);
            color: var(--white);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .brand-headline {
            font-size: 2.1rem;
            font-weight: 800;
            color: var(--white);
            line-height: 1.2;
            letter-spacing: -0.01em;
        }

        .brand-copy {
            font-size: 0.9rem;
            color: #cbd5e1;
            font-weight: 500;
            line-height: 1.6;
            max-width: 32rem;
        }

        .brand-affiliation {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .pulse-dot {
            width: 0.65rem;
            height: 0.65rem;
            border-radius: 50%;
            background-color: var(--hover-color);
            animation: pulse 1.6s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.35; }
        }

        .brand-footer {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        /* ---------- Right form panel ---------- */
        .form-panel {
            min-height: 100vh;
            background-color: var(--background-color);
            padding: 2.5rem 1.5rem;
        }

        @media (min-width: 992px) {
            .form-panel {
                padding: 4rem;
            }
        }

        .mobile-logo h2 {
            color: var(--dark-panel);
            font-weight: 800;
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .mobile-logo p {
            color: var(--highlight-color);
            font-weight: 700;
            font-size: 0.72rem;
            letter-spacing: 0.04em;
            margin-bottom: 0;
        }

        .form-wrapper {
            max-width: 26rem;
            width: 100%;
            margin: auto;
        }

        .form-heading h2 {
            color: var(--highlight-heading-color);
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .form-heading p {
            color: var(--general-color);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .alert-danger.custom {
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            color: #881337;
            border-radius: 1rem;
        }

        .alert-success.custom {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            border-radius: 1rem;
        }

        .form-label-custom {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--heading-color);
            margin-bottom: 0.5rem;
        }

        .input-icon-wrap {
            position: relative;
        }

        .input-icon-wrap .icon {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0.9rem;
            display: flex;
            align-items: center;
            color: #94a3b8;
            pointer-events: none;
        }

        .form-control-custom {
            padding: 0.85rem 1rem 0.85rem 2.6rem;
            background-color: var(--white);
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            color: var(--dark-panel);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .form-control-custom::placeholder {
            color: #94a3b8;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: transparent;
            box-shadow: 0 0 0 2px var(--highlight-color);
        }

        .invalid-feedback-custom {
            display: block;
            margin-top: 0.35rem;
            font-size: 0.78rem;
            color: var(--highlight-color);
        }

        .form-check-label-custom {
            font-size: 0.78rem;
            color: var(--general-color);
            font-weight: 500;
        }

        .form-check-input-custom {
            border-color: #cbd5e1;
        }

        .form-check-input-custom:checked {
            background-color: var(--btn-color);
            border-color: var(--btn-color);
        }

        .btn-signin {
            width: 100%;
            padding: 0.85rem 1rem;
            background-color: var(--btn-color);
            border: none;
            color: var(--white);
            font-weight: 700;
            font-size: 0.9rem;
            border-radius: 0.75rem;
            box-shadow: 0 6px 16px rgba(141, 34, 41, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background-color 0.2s ease, transform 0.15s ease;
        }

        .btn-signin:hover,
        .btn-signin:focus {
            background-color: var(--hover-color);
            color: var(--white);
        }

        .btn-signin:active {
            transform: scale(0.99);
        }

        .form-footer-mobile {
            font-size: 0.75rem;
            color: var(--general-color);
        }
    </style>
</head>
<body>

    <div class="d-flex flex-column flex-lg-row page-wrapper w-100">

        <!-- Left Side: Image & Branding Banner (hidden on mobile) -->
        <div class="d-none d-lg-flex col-lg-6 brand-panel">
            <div class="brand-panel__bg">
                <img src="{{ asset('images/login-banner.jpg') }}" alt="Informatics College Pokhara Campus">
                <div class="brand-panel__overlay"></div>
            </div>

            <div class="brand-panel__content">
                <!-- Top Logo -->
                <div class="d-flex align-items-center gap-3">
                    <div class="brand-logo-box">
                        <img src="{{ asset('images/icp-logo.png') }}" alt="ICP Logo">
                    </div>
                </div>

                <!-- Middle Feature Text -->
                <div class="my-auto py-5" style="max-width: 32rem;">
                    <span class="brand-badge mb-3 d-inline-flex">UK Higher Education in Nepal</span>
                    <h1 class="brand-headline mt-3 mb-3">Empowering Your Future in IT &amp; Business</h1>
                    <p class="brand-copy">
                        Admission Management Portal for Informatics College Pokhara. Seamlessly manage student enquiries, application reviews, and enrollment workflows.
                    </p>

                    <div class="brand-affiliation mt-4">
                        <div class="pulse-dot"></div>
                        <span class="text-white-50 fw-semibold" style="font-size: 0.72rem; color: #e2e8f0 !important;">
                            University Partner - London Metropolitan University, UK
                        </span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="brand-footer">
                    <p class="mb-0">&copy; {{ date('Y') }} Informatics College Pokhara. All rights reserved.</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="col-lg-6 d-flex flex-column justify-content-between form-panel">

            <!-- Mobile Top Logo -->
            <div class="d-flex d-lg-none align-items-center gap-3 mb-4 mobile-logo">
                <img src="{{ asset('images/icp-logo.png') }}" alt="ICP Logo" style="height:3rem;width:auto;">
                <div>
                    <h2>Informatics College</h2>
                    <p>POKHARA, NEPAL</p>
                </div>
            </div>

            <div class="form-wrapper py-3">
                <!-- Header -->
                <div class="form-heading mb-4">
                    <h2 class="fs-3 mb-1">Welcome Back</h2>
                    <p class="mb-0">Please sign in to access the Admission Management System</p>
                </div>

                <!-- Error Alerts -->
                @if ($errors->any())
                    <div class="alert alert-danger custom mb-4" role="alert">
                        @foreach ($errors->all() as $error)
                            <div class="d-flex align-items-start gap-2 mb-1">
                                <i class="bi bi-exclamation-triangle mt-1"></i>
                                <span class="fw-semibold small">{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success custom mb-4" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle"></i>
                            <span class="small fw-medium">{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label-custom d-block">Email Address</label>
                        <div class="input-icon-wrap">
                            <span class="icon"><i class="bi bi-person-circle"></i></span>
                            <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus
                                placeholder="Enter Email Address"
                                class="form-control form-control-custom @error('email') is-invalid @enderror">
                        </div>
                        @error('email')
                            <span class="invalid-feedback-custom">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label-custom d-block">Password</label>
                        <div class="input-icon-wrap">
                            <span class="icon"><i class="bi bi-lock-fill"></i></span>
                            <input id="password" type="password" name="password" required
                                placeholder="Enter Password"
                                class="form-control form-control-custom @error('password') is-invalid @enderror">
                        </div>
                        @error('password')
                            <span class="invalid-feedback-custom">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="d-flex align-items-center justify-content-between mb-4 pt-1">
                        <div class="form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input form-check-input-custom" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember" class="form-check-label form-check-label-custom">Keep me signed in</label>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-signin">
                        <span>Sign In to Dashboard</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>

            <!-- Mobile Footer -->
            <div class="d-lg-none text-center form-footer-mobile pt-4">
                <p class="mb-0">&copy; {{ date('Y') }} Informatics College Pokhara. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons (for the icons used above) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>