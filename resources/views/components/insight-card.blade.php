@props(['insight'])

<a
    href="{{ route('insights.show', $insight) }}"
    {{ $attributes->merge(['class' => 'group block rounded-2xl border border-stone-200 bg-white p-6 shadow-sm transition hover:border-mustard-300']) }}
>
    <time datetime="{{ $insight->published_at->toDateString() }}" class="text-sm text-stone-500">
        {{ $insight->published_at->format('M j, Y') }}
    </time>
    <h3 class="mt-2 font-semibold group-hover:text-mustard-700">{{ $insight->title }}</h3>
    <p class="mt-2 text-stone-600">{{ $insight->excerpt }}</p>
</a>
