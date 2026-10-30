@php
    $projects = [
        [
            'title' => 'This website',
            'year' => '2026',
            'category' => 'Full-stack',
            'description' => 'My working lab and blog, built from scratch to learn Docker and CI/CD properly. A containerised Laravel and React app that is tested and packaged automatically on every push.',
            'image' => 'images/projects/website.png',
            'image_alt' => 'The home page of Andrea Bonola\'s website, with the headline "Hi, I\'m Andrea Bonola."',
            'tags' => ['Laravel', 'React', 'TypeScript', 'Tailwind CSS', 'Docker', 'GitHub Actions', 'PostgreSQL'],
            'links' => [
                ['label' => 'Read the story', 'url' => route('insights.show', 'building-my-website-part-1-why-i-built-it-and-the-tools-i-chose'), 'external' => false],
                ['label' => 'Source code', 'url' => 'https://github.com/AMBONOLA/website', 'external' => true],
            ],
        ],
    ];
@endphp

<x-layout title="Projects" description="Things Andrea Bonola has built, and what was learned building them.">
    <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">
        Projects<span class="text-mustard-500">.</span>
    </h1>

    <p class="mt-4 max-w-2xl text-lg leading-relaxed text-stone-600">
        Things I've built, and what I learned building them.
    </p>

    <div class="mt-8 flex items-center gap-4">
        <div class="h-1.5 w-16 rounded-full bg-mustard-500"></div>
        <p class="text-sm text-stone-500">{{ trans_choice(':count project|:count projects', count($projects)) }}</p>
    </div>

    <div class="mt-12 divide-y divide-stone-200">
        @foreach ($projects as $project)
            <article class="grid gap-8 py-10 first:pt-0 sm:grid-cols-5 sm:items-center">
    
                <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm sm:col-span-2">
                    <div class="flex gap-1.5 border-b border-stone-200 bg-stone-100 px-3 py-2" aria-hidden="true">
                        <span class="h-2.5 w-2.5 rounded-full bg-stone-300"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-stone-300"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-mustard-400"></span>
                    </div>
                    <img
                        src="{{ asset($project['image']) }}?v={{ filemtime(public_path($project['image'])) }}"
                        alt="{{ $project['image_alt'] }}"
                        loading="lazy"
                        class="aspect-[16/10] w-full object-cover object-top"
                    >
                </div>

                <div class="sm:col-span-3">
                    <p class="flex items-center gap-2 text-sm font-medium text-stone-500">
                        <span class="h-2 w-2 rounded-full bg-mustard-500"></span>
                        {{ $project['year'] }} &middot; {{ $project['category'] }}
                    </p>

                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight">{{ $project['title'] }}</h2>

                    <p class="mt-3 leading-relaxed text-stone-600">{{ $project['description'] }}</p>

                    <ul class="mt-4 flex flex-wrap gap-2" aria-label="Built with">
                        @foreach ($project['tags'] as $tag)
                            <li class="rounded-full bg-white px-2.5 py-0.5 text-xs font-medium text-stone-600 ring-1 ring-stone-200">{{ $tag }}</li>
                        @endforeach
                    </ul>

                    <div class="mt-6 flex flex-wrap gap-x-6 gap-y-2 text-sm font-semibold">
                        @foreach ($project['links'] as $link)
                            <a
                                href="{{ $link['url'] }}"
                                @if ($link['external']) target="_blank" rel="noopener" @endif
                                class="inline-flex items-center gap-1 text-stone-900 underline decoration-mustard-500 decoration-2 underline-offset-4 transition hover:text-mustard-700"
                            >
                                {{ $link['label'] }}
                                {{-- "arrow-up-right" / "arrow-right" icons from Feather (MIT licence). --}}
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    @if ($link['external'])
                                        <line x1="7" y1="17" x2="17" y2="7"></line>
                                        <polyline points="7 7 17 7 17 17"></polyline>
                                    @else
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    @endif
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</x-layout>
