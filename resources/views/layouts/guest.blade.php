<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CLT Manager') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .auth-bg {
            background: linear-gradient(135deg, #1E3A0E 0%, #2D5016 30%, #3D6B22 60%, #4A7C2E 100%);
            position: relative;
            overflow: hidden;
        }

        .auth-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            pointer-events: none;
            background: radial-gradient(ellipse at 30% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 60%),
                radial-gradient(ellipse at 70% 20%, rgba(255, 255, 255, 0.03) 0%, transparent 40%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(-2%, 2%);
            }
        }

        .auth-card {
            background: #FFFFFF;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.5s ease-out;
            position: relative;
            z-index: 1;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-input {
            width: 100%;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            background-color: #FFFFFF;
            color: #1F2937;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .auth-input:focus {
            outline: none;
            border-color: #2D5016;
            box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
        }

        .auth-btn {
            width: 100%;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #FFFFFF;
            background-color: #2D5016;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .auth-btn:hover {
            background-color: #3D6B22;
        }

        .auth-btn:active {
            background-color: #1E3A0E;
        }

        .auth-link {
            color: #2D5016;
            font-size: 0.8125rem;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .auth-link:hover {
            color: #3D6B22;
            text-decoration: underline;
        }

        .auth-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.375rem;
        }

        .auth-error {
            margin-top: 0.25rem;
            font-size: 0.8125rem;
            color: #DC2626;
        }

        .auth-checkbox {
            width: 1rem;
            height: 1rem;
            border-radius: 0.25rem;
            border: 1px solid #D1D5DB;
            accent-color: #2D5016;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen auth-bg flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Logo -->
        <div class="mb-6" style="animation: slideUp 0.4s ease-out; position: relative; z-index: 1;">
            <a href="/" class="flex items-center gap-3">
                <svg class="w-10 h-10" viewBox="0 0 40 40" fill="none">
                    <rect width="40" height="40" rx="10" fill="rgba(255,255,255,0.15)" />
                    <path d="M12 14h16v3H12zM12 19h16v3H12zM12 24h16v3H12z" fill="#FFFFFF" opacity="0.9" />
                    <path d="M14 14v13" stroke="#FFFFFF" stroke-width="1.5" opacity="0.5" />
                    <path d="M20 14v13" stroke="#FFFFFF" stroke-width="1.5" opacity="0.5" />
                    <path d="M26 14v13" stroke="#FFFFFF" stroke-width="1.5" opacity="0.5" />
                </svg>
                <span class="text-xl font-bold text-white tracking-tight">CLT Manager</span>
            </a>
        </div>

        <!-- Auth Card -->
        <div class="w-full sm:max-w-md auth-card overflow-hidden">
            <div class="px-8 py-8">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-6 text-xs text-white/40" style="animation: slideUp 0.6s ease-out; position: relative; z-index: 1;">
            &copy; {{ date('Y') }} CLT Manager &middot; All rights reserved.
        </p>
    </div>
</body>

</html>
