<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

{{--        <title>{{ $page_title ?? config('app.name', 'Laravel') }}</title>--}}

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">

        <link rel="stylesheet" href="{{ asset('css/highlightjs-dark.css') }}" title="highlight-dark">
        <link rel="stylesheet" href="{{ asset('css/highlightjs-light.css') }}" title="highlight-light">

{{--        <link rel="stylesheet"--}}
{{--              href="//unpkg.com/@highlightjs/cdn-assets@11.2.0/styles/default.min.css">--}}

        <script src="https://d3js.org/d3.v7.min.js"></script>
        <script src="{{ asset('js/app.js') }}" defer></script>

        <script>
            // On page load or when changing themes, best to add inline in `head` to avoid FOUC
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                window.theme = 'dark'
                document.documentElement.classList.add('dark')
                document.querySelector('link[title="highlight-dark"]').removeAttribute('disabled', 'disabled');
                document.querySelector('link[title="highlight-light"]').setAttribute('disabled', 'disabled');
            } else {
                window.theme = 'light'
                document.documentElement.classList.remove('dark')
                localStorage.theme = 'light'
                document.querySelector('link[title="highlight-light"]').removeAttribute('disabled', 'disabled');
                document.querySelector('link[title="highlight-dark"]').setAttribute('disabled', 'disabled');
            }

            window.toggleTheme = function() {
                let theme = (localStorage.theme === 'dark' ? 'light' : 'dark');
                window.theme = theme;
                localStorage.theme = theme;
                document.documentElement.classList.toggle('dark', theme === 'dark');
                document.querySelector('link[title="highlight-light"]').toggleAttribute('disabled', theme === 'dark');
                document.querySelector('link[title="highlight-dark"]').toggleAttribute('disabled', theme !== 'dark');
            }
        </script>
    </head>
    <body>
        @inertia
    </body>
</html>
