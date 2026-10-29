<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- SEO -->
        <meta name="description" content="Metis — Sistem Pakar Diagnosis Dini Stunting Posyandu Melati Pujer, Bondowoso">
        <meta name="author" content="Muhammad Farhan Maulana — Politeknik Negeri Jember">

        <!-- Inertia title -->
        <title inertia>{{ config('app.name', 'Metis — SiPakar Stunting') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="/assets/icons/favicon.svg">
        <link rel="icon" type="image/png" href="/assets/icons/favicon.png">

        <!-- Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Metis Theme Init (instant dark/light mode, zero flash) -->
        <script>
            (function() {
                const theme = localStorage.getItem('metis-theme') || localStorage.getItem('orbit-theme') || 'light';
                document.documentElement.setAttribute('data-bs-theme', theme);
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
    <body class="font-sans antialiased bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 min-h-screen transition-colors duration-150 admin-layout">
        @inertia
    </body>
</html>
