<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My App')</title>

</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">

<!-- Content -->
<main class="flex-1 container mx-auto py-6 px-4">
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-white shadow
    <!-- Footer -->
    <footer class="bg-white shadow-md mt-8 py-4">
<div class="container mx-auto text-center text-gray-500">
    <p>&copy; {{ date('Y') }} MyApp. All rights reserved.</p>
</div>
</footer>

</body>
</html>
