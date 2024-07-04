<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    {{-- <link rel="stylesheet" href="{{ asset('css/styles.css') }}"> --}}
    @stack('styles')
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>
