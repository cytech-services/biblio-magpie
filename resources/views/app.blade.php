<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">

        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

        @production
            @php
                $manifest = json_decode(file_get_contents(public_path('dist/manifest.json')), true);
            @endphp
            <script type="module" src="/dist/{{ $manifest['resources/js/app.js']['file'] }}"></script>
            <link rel="stylesheet" href="/dist/{{ $manifest['resources/js/app.js']['css'][0] }}">
        @else
            <script type="module" src="http://localhost:3030/resources/js/app.js"></script>
        @endproduction

        @routes
    </head>
    <body class="h-full bg-stone-50 dark:bg-stone-900 text-stone-800 dark:text-stone-100">
        @inertia
    </body>
</html>
