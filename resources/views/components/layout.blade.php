@props([
    'title' => null,
    'description' => 'Andrea Bonola — Full-Stack Web Developer',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $description }}">

        <title>{{ $title ? $title.' — Andrea Bonola' : 'Andrea Bonola — Full-Stack Web Developer' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,800&display=swap" rel="stylesheet" />

        @vite('resources/css/app.css')
    </head>
    <body class="flex min-h-screen flex-col bg-stone-50 font-sans text-stone-900 antialiased selection:bg-mustard-300">
        <header class="mx-auto flex w-full max-w-4xl items-center justify-between gap-6 px-6 py-8">
            <a href="{{ route('home') }}" class="text-lg font-extrabold tracking-tight">
                AB<span class="text-mustard-500">.</span>
            </a>

            <nav class="flex items-center gap-6 text-sm font-medium">
                @foreach ([
                    'insights.index' => ['label' => 'Insights', 'active' => 'insights.*'],
                    'projects' => ['label' => 'Projects', 'active' => 'projects'],
                    'about' => ['label' => 'About', 'active' => 'about'],
                ] as $route_name => $link)
                    <a
                        href="{{ route($route_name) }}"
                        @class([
                            'border-b-2 pb-0.5 transition',
                            'border-mustard-500 text-stone-900' => request()->routeIs($link['active']),
                            'border-transparent text-stone-500 hover:text-stone-900' => ! request()->routeIs($link['active']),
                        ])
                        @if (request()->routeIs($link['active'])) aria-current="page" @endif
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>
        </header>

        <main {{ $attributes->merge(['class' => 'mx-auto flex w-full max-w-4xl flex-1 flex-col px-6 pb-16 sm:pb-24']) }}>
            {{ $slot }}
        </main>

        <footer class="border-t border-stone-200">
            <div class="mx-auto flex w-full max-w-4xl items-center justify-between gap-4 px-6 py-6 text-sm text-stone-500">
                <p>&copy; {{ date('Y') }} Andrea Bonola</p>

                {{-- Icons from Feather (MIT licence). --}}
                <div class="flex items-center gap-4">
                    <a href="https://github.com/AMBONOLA" target="_blank" rel="noopener" aria-label="GitHub" class="transition hover:text-mustard-600">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path>
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/in/andrea-bonola-331a85186" target="_blank" rel="noopener" aria-label="LinkedIn" class="transition hover:text-mustard-600">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                            <rect x="2" y="9" width="4" height="12"></rect>
                            <circle cx="4" cy="4" r="2"></circle>
                        </svg>
                    </a>
                    <a href="mailto:andreabonola@gmail.com" aria-label="Email" class="transition hover:text-mustard-600">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </footer>
    </body>
</html>
