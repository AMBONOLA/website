<x-layout :title="$insight->title" :description="$insight->excerpt">
    <article class="relative mx-auto w-full max-w-3xl">
        {{-- Decorative colour wash behind the header. --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 -top-16 -z-10 h-96 overflow-hidden">
            <div class="absolute left-1/2 top-0 h-72 w-[36rem] -translate-x-1/2 rounded-full bg-mustard-200/60 blur-3xl"></div>
            <div class="absolute right-0 top-16 h-48 w-64 rounded-full bg-mustard-100 blur-3xl"></div>
        </div>

        <a href="{{ route('insights.index') }}" class="text-sm font-medium text-stone-500 transition hover:text-stone-900">
            &larr; All insights
        </a>

        <div class="mt-8 flex flex-wrap items-center gap-2 text-sm font-medium">
            <time datetime="{{ $insight->published_at->toDateString() }}" class="inline-flex items-center gap-2 rounded-full bg-mustard-100 px-3 py-1 text-mustard-800">
                <span class="h-2 w-2 rounded-full bg-mustard-500"></span>
                {{ $insight->published_at->format('F j, Y') }}
            </time>
            <span class="rounded-full bg-white/70 px-3 py-1 text-stone-600 ring-1 ring-stone-200">
                {{ $insight->readingMinutes() }} min read
            </span>
        </div>

        <h1 class="mt-5 text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl">
            {{ $insight->title }}<span class="text-mustard-500">.</span>
        </h1>

        <p class="mt-5 text-lg leading-relaxed text-stone-600 sm:text-xl">
            {{ $insight->excerpt }}
        </p>

        <div class="mt-8 h-1.5 w-16 rounded-full bg-mustard-500"></div>

        {{-- The body is sanitized by Insight::sanitizeHtml(), so it is safe to output unescaped. --}}
        <div class="insight-body mt-10 rounded-3xl border border-stone-200 bg-white px-6 py-8 shadow-sm sm:px-12 sm:py-12">
            {!! $insight->body !!}
        </div>

        <aside class="mt-10 flex flex-col gap-4 rounded-3xl bg-mustard-100 px-6 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-10">
            <div>
                <p class="text-lg font-extrabold tracking-tight text-stone-900">
                    Thanks for reading<span class="text-mustard-600">.</span>
                </p>
                <p class="mt-1 text-stone-700">More notes on what I'm building and learning are on the way.</p>
            </div>
            <a href="{{ route('insights.index') }}" class="inline-flex w-fit items-center rounded-full bg-stone-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-mustard-700">
                All insights &rarr;
            </a>
        </aside>
    </article>
</x-layout>
