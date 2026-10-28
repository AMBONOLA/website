<x-layout title="About" description="About Andrea Bonola, a full-stack software engineer in North Carolina.">
    <section class="flex flex-col gap-8 sm:flex-row sm:items-center">
        @if (file_exists(public_path('images/andrea-web.jpg')))
            <img
                src="{{ asset('images/andrea-web.jpg') }}?v={{ filemtime(public_path('images/andrea-web.jpg')) }}"
                alt="Andrea Bonola"
                class="h-36 w-36 shrink-0 rounded-full object-cover object-[50%_75%] ring-4 ring-mustard-300 sm:h-44 sm:w-44"
            >
        @else
            <div class="flex h-36 w-36 shrink-0 items-center justify-center rounded-full bg-mustard-100 text-4xl font-extrabold tracking-tight ring-4 ring-mustard-300 sm:h-44 sm:w-44">
                AB<span class="text-mustard-500">.</span>
            </div>
        @endif

        <div>
            <p class="mb-4 inline-flex w-fit items-center gap-2 rounded-full bg-mustard-100 px-3 py-1 text-sm font-medium text-mustard-800">
                <span class="h-2 w-2 rounded-full bg-mustard-500"></span>
                North Carolina, United States
            </p>

            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">
                About me<span class="text-mustard-500">.</span>
            </h1>

            <p class="mt-4 max-w-2xl text-lg leading-relaxed text-stone-600">
                I'm Andrea, a full-stack software engineer with 3+ years of experience. Away from
                the keyboard, you'll find me chasing waterfalls, on the jiu-jitsu mats, or out in
                the community with my Rotary club.
            </p>
        </div>
    </section>

    <div class="mt-12 h-1.5 w-16 rounded-full bg-mustard-500"></div>

    <section class="mt-12">
        <h2 class="text-2xl font-extrabold tracking-tight">
            At work<span class="text-mustard-500">.</span>
        </h2>

        <div class="insight-body mt-6 max-w-2xl">
            <p>
                I build the parts people see and the systems behind them: scalable data
                architectures, centralized APIs, fast databases, and containerized deployments. I
                care most about reliability: systems that keep working when the data gets big,
                deployments that don't break on a Friday, and queries that stay fast at a hundred
                thousand rows.
            </p>
            <p>
                I learn best by building, and this site is one of those projects: Laravel and React
                running in Docker, shipped through a CI/CD pipeline that won't deploy a commit
                unless its tests pass. What I pick up along the way, I share as
                <a href="{{ route('insights.index') }}">insights</a>.
            </p>
        </div>
    </section>

    <section class="mt-16">
        <h2 class="text-2xl font-extrabold tracking-tight">
            Outside of work<span class="text-mustard-500">.</span>
        </h2>

        <div class="mt-8 grid gap-4 sm:grid-cols-3">
            @foreach ([
                [
                    'label' => 'Outdoors',
                    'title' => 'Chasing waterfalls',
                    'text' => "I'm happiest outside, and I'm working my way through North Carolina's 500 Waterfall Challenge, one trail at a time.",
                ],
                [
                    'label' => 'Brazilian jiu-jitsu',
                    'title' => 'Blue belt',
                    'text' => "I train Brazilian jiu-jitsu and compete at least once a year. It's problem-solving under pressure, just with less typing.",
                ],
                [
                    'label' => 'Community',
                    'title' => 'Rotarian',
                    'text' => "I'm a member of Rotary and spend a lot of my time on community service, giving back to the place I call home.",
                ],
            ] as $interest)
                <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-mustard-700">{{ $interest['label'] }}</p>
                    <h3 class="mt-2 font-semibold">{{ $interest['title'] }}</h3>
                    <p class="mt-2 text-stone-600">{{ $interest['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mt-16">
        <h2 class="text-2xl font-extrabold tracking-tight">
            What I work with<span class="text-mustard-500">.</span>
        </h2>

        <div class="mt-8 grid gap-8 sm:grid-cols-2">
            @foreach ([
                'Back end & systems' => ['PHP (Laravel)', 'Python', 'Java', 'PostgreSQL', 'MSSQL'],
                'Front end & APIs' => ['React', 'JavaScript', 'HTML & CSS', 'Bootstrap', 'REST APIs', 'SAML', 'Third-party integrations'],
                'Data' => ['Advanced SQL', 'CTEs & window functions', 'Stored procedures', 'Indexing & query optimization', 'Data pipelines', 'Azure Data Lake'],
                'Tools & infrastructure' => ['Docker', 'Azure Container Apps', 'AWS', 'CI/CD', 'Git & GitHub', 'Linux (RHEL, Ubuntu)', 'Cron jobs'],
            ] as $group => $skills)
                <div>
                    <h3 class="font-semibold">{{ $group }}</h3>
                    <ul class="mt-3 flex flex-wrap gap-2">
                        @foreach ($skills as $skill)
                            <li class="rounded-full border border-stone-200 bg-white px-3 py-1 text-sm text-stone-700">{{ $skill }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>
</x-layout>
