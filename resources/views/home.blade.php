<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Andrea Bonola — Full-Stack Web Developer">

        <title>Andrea Bonola — Full-Stack Web Developer</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,800&display=swap" rel="stylesheet" />

        @vite('resources/css/app.css')
    </head>
    <body class="flex min-h-screen flex-col bg-stone-50 font-sans text-stone-900 antialiased selection:bg-mustard-300">
        <header class="mx-auto flex w-full max-w-4xl items-center justify-between px-6 py-8">
            <a href="{{ route('home') }}" class="text-lg font-extrabold tracking-tight">
                AB<span class="text-mustard-500">.</span>
            </a>
        </header>

        <main class="mx-auto flex w-full max-w-4xl flex-1 flex-col justify-center px-6 pb-24">
            <p class="mb-4 inline-flex w-fit items-center gap-2 rounded-full bg-mustard-100 px-3 py-1 text-sm font-medium text-mustard-800">
                <span class="h-2 w-2 rounded-full bg-mustard-500"></span>
                Full-Stack Engineer
            </p>

            <h1 class="text-5xl font-extrabold leading-tight tracking-tight sm:text-7xl">
                Hi, I'm Andrea Bonola<span class="text-mustard-500">.</span>
            </h1>

            <p class="mt-6 max-w-2xl text-lg leading-relaxed text-stone-600 sm:text-xl">
                I'm a web developer who builds things end to end, from the database and
                APIs on the back end to the interfaces people actually use.
            </p>

            <div class="mt-10 h-1.5 w-24 rounded-full bg-mustard-500"></div>

            <section class="mt-16 grid gap-8 sm:grid-cols-3">
                <div>
                    <h2 class="font-semibold">Back end</h2>
                    <p class="mt-2 text-stone-600">APIs, databases and the server-side logic that holds it all together.</p>
                </div>
                <div>
                    <h2 class="font-semibold">Front end</h2>
                    <p class="mt-2 text-stone-600">Clean, responsive interfaces that are easy to use.</p>
                </div>
                <div>
                    <h2 class="font-semibold">Everything between</h2>
                    <p class="mt-2 text-stone-600">Taking a product from idea to deployment.</p>
                </div>
            </section>
        </main>

        <footer class="border-t border-stone-200">
            <div class="mx-auto w-full max-w-4xl px-6 py-6 text-sm text-stone-500">
                &copy; {{ date('Y') }} Andrea Bonola
            </div>
        </footer>
    </body>
</html>
