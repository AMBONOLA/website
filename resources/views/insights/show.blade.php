<x-layout :title="$insight->title" :description="$insight->excerpt">
    <article class="max-w-2xl">
        <a href="{{ route('insights.index') }}" class="text-sm font-medium text-stone-500 hover:text-stone-900">
            &larr; All insights
        </a>

        <h1 class="mt-6 text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl">
            {{ $insight->title }}<span class="text-mustard-500">.</span>
        </h1>

        <time datetime="{{ $insight->published_at->toDateString() }}" class="mt-4 block text-stone-500">
            {{ $insight->published_at->format('F j, Y') }}
        </time>

        <div class="mt-8 h-1.5 w-16 rounded-full bg-mustard-500"></div>

        {{-- bodyHtml() escapes raw HTML in the Markdown, so it is safe to output unescaped. --}}
        <div class="insight-body mt-10">
            {!! $insight->bodyHtml() !!}
        </div>
    </article>
</x-layout>
