<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Successful - JaniKoke</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS (via CDN purely for this isolated blade view as vite only exposes inertia's app.jsx) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-100">
    <div class="flex flex-col items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-md p-8 text-center bg-white shadow-lg rounded-2xl">
            <!-- Success icon -->
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            @php
                // Retrieve user data - either from active auth session or request data
                $googleUser = request()->user();
            @endphp

            <h1 class="mb-2 text-2xl font-bold text-gray-800">Login Successful!</h1>

            @if($googleUser && $googleUser->email && $googleUser->email !== '—')
                <p class="mb-1 text-gray-500">You have successfully logged in as</p>
                <p class="mb-6 text-lg font-semibold text-indigo-600">
                    {{ $googleUser->email }}
                </p>
            @endif

            @if($googleUser && ($googleUser->name ?? false))
                <p class="mb-6 text-sm text-gray-400">Welcome, {{ $googleUser->name }} 👋</p>
            @endif

            <a
                href="{{ route('home') }}"
                class="block w-full px-5 py-2.5 text-sm font-semibold text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700"
            >
                Go to Home
            </a>
        </div>
    </div>
</body>
</html>
