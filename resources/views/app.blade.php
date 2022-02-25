<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

        @production
            @php
                $manifest = json_decode(file_get_contents(public_path('dist/manifest.json')), true);
            @endphp
            <script type="module" src="/dist/{{ $manifest['resources/js/app.ts']['file'] }}"></script>
            <link rel="stylesheet" href="/dist/{{ $manifest['resources/js/app.ts']['css'][0] }}">
        @else
            <script type="module" src="http://localhost:3030/resources/js/app.ts"></script>
        @endproduction

        @routes
    </head>
    <body class="h-full bg-stone-100 bg-stone-100 dark:bg-stone-900 text-stone-800 dark:text-stone-100">
        @inertia
    </body>
</html>
