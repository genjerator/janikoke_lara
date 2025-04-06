<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Janikoke</title>
    <meta name="description" content="Jani koke fun club.">

    <!-- Open Graph meta tags -->
    <meta property="og:title" content="Janikoke">
    <meta property="og:description" content="Jani koke fun club.">
    <meta property="og:image" content="{{ config('app.url') }}/band.jpg">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:type" content="website">

    <!-- Twitter Card meta tags (optional but recommended) -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Janikoke">
    <meta name="twitter:description" content="Jani Koke fun club.">
    <meta name="twitter:image" content="{{ config('app.url') }}/band.jpg">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    <style>
        /* Layout */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        .page-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .title {
            font-size: 1.25rem;
            font-weight: bold;
        }

        /* Search Field & Button */
        .search-container {
            display: flex;
            gap: 0.5rem;
        }

        .search-input {
            padding: 0.5rem 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.2s;
        }

        .search-input:focus {
            border-color: #3b82f6;
        }

        .search-button {
            padding: 0.5rem 1rem;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .search-button:hover {
            background-color: #2563eb;
        }

        /* Map Section */
        .map-section {
            flex: 1;
            border-top: 1px solid #ddd;
        }

        .map-container {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
<div class="page-container">
    <!-- Header -->
    <header class="header">
        <h1 class="title">Map App</h1>
        <div class="search-container">
            <input type="text" placeholder="Search..." class="search-input">
            <button class="search-button">Search</button>
        </div>
    </header>

    <!-- Map Section -->
    <main class="map-section">
        <div class="map-container">
            {!! $map !!}
        </div>
    </main>
</div>

</body>

</html>
