<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Failed - JaniKoke</title>
    
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
            <!-- Error icon -->
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            <h1 class="mb-2 text-2xl font-bold text-gray-800">Login Failed</h1>

            <p class="mb-4 text-gray-500">Something went wrong during Google login.</p>

            @if(isset($error) && !empty($error))
                <div class="px-4 py-3 mb-6 text-sm text-left text-red-700 border border-red-200 rounded-lg bg-red-50">
                    <span class="font-semibold">Error: </span>{{ $error }}
                </div>
            @endif

            <a
                href="{{ route('home') }}"
                class="block w-full px-5 py-2.5 text-sm font-semibold text-white transition bg-gray-800 rounded-lg hover:bg-gray-700"
            >
                Try Again
            </a>
        </div>
    </div>
</body>
</html>
