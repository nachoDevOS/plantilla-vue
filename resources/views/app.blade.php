<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';
                const component = @js($page['component'] ?? '');

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }

                document.documentElement.classList.add('app-loading');

                if (component === 'Welcome' || component === 'public/Welcome') {
                    document.documentElement.classList.add('app-landing-loading');
                }

                window.setTimeout(function() {
                    document.documentElement.classList.add('app-ready');
                    document.documentElement.classList.remove('app-loading', 'app-landing-loading');
                }, 3000);
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }

            html.app-landing-loading.app-loading:not(.app-ready),
            html.app-landing-loading.app-loading:not(.app-ready) body {
                background-color: #0f172a;
            }

            html.app-loading:not(.app-ready) #app {
                opacity: 0;
            }

            html.app-ready #app {
                opacity: 1;
                transition: opacity 120ms ease;
            }
        </style>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
