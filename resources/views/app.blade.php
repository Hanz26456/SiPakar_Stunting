<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- SEO -->
        <meta name="description" content="Sistem Pakar Diagnosis Dini Stunting — Posyandu Melati Pujer, Bondowoso">
        <meta name="author" content="Muhammad Farhan Maulana — Politeknik Negeri Jember">

        <!-- Inertia title -->
        <title inertia>{{ config('app.name', 'SiPakar Stunting') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Orbit Theme Init (instant dark/light mode, no flash) -->
        <script>
            (function() {
                const theme = localStorage.getItem('orbit-theme') || 'dark';
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>

        <!-- Scripts + Styles (Vite) -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-orbit-bg min-h-screen transition-colors duration-200">
        @inertia
    </body>
</html>