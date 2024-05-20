<!-- resources/views/components/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Livewire CRUD</title>
    @livewireStyles
</head>
<body>
<div class="container mx-auto">
    {{ $slot }}
</div>
@livewireScripts
</body>
</html>
