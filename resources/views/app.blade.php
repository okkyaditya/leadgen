<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="Leads Tracker - Next-Gen platform to maximize sales, track leads, and accelerate commercial growth.">
        <meta name="keywords" content="leads tracker, lead gen, sales tracking, client database, pipeline management, sales analytics">
        <meta name="author" content="Leads Tracker Team">
        <meta name="robots" content="index, follow">

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="Leads Tracker - Next-Gen Analytics Platform">
        <meta property="og:description" content="Maximize sales, track leads, and grow faster. Collaborate seamlessly with your sales team.">
        <meta property="og:image" content="{{ asset('images/icons8-magnet-96.png') }}">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="Leads Tracker - Next-Gen Analytics Platform">
        <meta property="twitter:description" content="Maximize sales, track leads, and grow faster. Collaborate seamlessly with your sales team.">
        <meta property="twitter:image" content="{{ asset('images/icons8-magnet-96.png') }}">

        <!-- Favicons -->
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/icons8-magnet-96.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/icons8-magnet-96.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Dark mode inline script to prevent flash of white -->
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Scripts -->
        @routes
        @viteReactRefresh
        @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
