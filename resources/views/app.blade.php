<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="/favicon.svg">

        @routes
        @vite(['resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="flex flex-col min-h-screen antialiased">
        @inertia
    </body>
</html>
