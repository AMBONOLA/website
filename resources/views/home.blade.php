<x-layout class="justify-center">
    <p class="mb-4 inline-flex w-fit items-center gap-2 rounded-full bg-mustard-100 px-3 py-1 text-sm font-medium text-mustard-800">
        <span class="h-2 w-2 rounded-full bg-mustard-500"></span>
        Full-Stack Engineer
    </p>

    <h1 class="text-4xl font-extrabold leading-tight tracking-tight sm:text-7xl">
        Hi, I'm Andrea Bonola<span class="text-mustard-500">.</span>
    </h1>

    <p class="mt-6 max-w-2xl text-lg leading-relaxed text-stone-600 sm:text-xl">
        I'm a web developer who builds things end to end, from the database and
        APIs on the back end to the interfaces people actually use.
    </p>

    <div class="mt-10 h-1.5 w-24 rounded-full bg-mustard-500"></div>

    <section class="mt-12 grid gap-8 sm:mt-16 sm:grid-cols-3">
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

    @if ($latest_insights->isNotEmpty())
        <section class="mt-16 sm:mt-24">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="text-2xl font-extrabold tracking-tight">
                    Latest insights<span class="text-mustard-500">.</span>
                </h2>
                <a href="{{ route('insights.index') }}" class="text-sm font-medium text-stone-500 hover:text-stone-900">
                    All insights &rarr;
                </a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                @foreach ($latest_insights as $insight)
                    <x-insight-card :insight="$insight" />
                @endforeach
            </div>
        </section>
    @endif
</x-layout>
