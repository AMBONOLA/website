<x-layout title="Insights" description="Tidbits Andrea Bonola has picked up while building things.">
    <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">
        Insights<span class="text-mustard-500">.</span>
    </h1>

    <p class="mt-4 max-w-2xl text-lg leading-relaxed text-stone-600">
        Tidbits I've picked up while building things: what broke, what I learned, and what I'd do differently.
    </p>

    <div class="mt-8 h-1.5 w-16 rounded-full bg-mustard-500"></div>

    @if ($insights->isEmpty())
        <p class="mt-12 text-stone-500">Nothing here yet. The first insight is on its way.</p>
    @else
        <div class="mt-12 grid gap-4">
            @foreach ($insights as $insight)
                <x-insight-card :insight="$insight" />
            @endforeach
        </div>

        @if ($insights->hasPages())
            <nav class="mt-10 flex justify-between text-sm font-medium" aria-label="Pagination">
                @if ($insights->previousPageUrl())
                    <a href="{{ $insights->previousPageUrl() }}" class="text-stone-500 hover:text-stone-900">&larr; Newer</a>
                @else
                    <span></span>
                @endif

                @if ($insights->nextPageUrl())
                    <a href="{{ $insights->nextPageUrl() }}" class="text-stone-500 hover:text-stone-900">Older &rarr;</a>
                @endif
            </nav>
        @endif
    @endif
</x-layout>
