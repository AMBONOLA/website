import InsightStatusBadge from '@/Components/InsightStatusBadge';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { ReactNode } from 'react';

const REPOSITORY_URL = 'https://github.com/AMBONOLA/website';

interface DatabaseStatus {
    connected: boolean;
    latency_ms: number | null;
}

interface SeriesPart {
    id: number;
    part: number;
    title: string;
    slug: string;
    is_published: boolean;
}

export default function Dashboard({
    commit,
    database,
    series,
}: {
    commit: string | null;
    database: DatabaseStatus;
    series: SeriesPart[];
}) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-3xl font-extrabold tracking-tight">
                    Dashboard<span className="text-mustard-500">.</span>
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="pb-12 pt-2">
                <div className="mx-auto grid max-w-7xl items-start gap-6 sm:px-6 lg:grid-cols-3 lg:px-8">
                    <Panel title="Site health">
                        <dl className="flex flex-col gap-4 p-6">
                            <div>
                                <dt className="text-sm text-stone-500">
                                    Running commit
                                </dt>
                                <dd className="mt-1 font-semibold">
                                    {commit ? (
                                        <a
                                            href={`${REPOSITORY_URL}/commit/${commit}`}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="font-mono hover:text-mustard-700"
                                        >
                                            {commit.slice(0, 7)} ↗
                                        </a>
                                    ) : (
                                        'Not recorded (local build)'
                                    )}
                                </dd>
                            </div>

                            <div>
                                <dt className="text-sm text-stone-500">
                                    Database
                                </dt>
                                <dd className="mt-1 flex items-center gap-2 font-semibold">
                                    <span
                                        aria-hidden="true"
                                        className={`h-2.5 w-2.5 rounded-full ${database.connected ? 'bg-green-600' : 'bg-red-600'}`}
                                    />
                                    {database.connected
                                        ? `Connected (${database.latency_ms} ms)`
                                        : 'Not connected'}
                                </dd>
                            </div>
                        </dl>
                    </Panel>

                    <div className="lg:col-span-2">
                        <SeriesPanel series={series} />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

function SeriesPanel({ series }: { series: SeriesPart[] }) {
    const publishedCount = series.filter((part) => part.is_published).length;

    return (
        <Panel title="Building my website">
            {series.length === 0 ? (
                <p className="p-6 text-stone-600">
                    No parts yet. Start a draft titled "Building my website,
                    part 1: ..." and it will appear here.
                </p>
            ) : (
                <>
                    <div className="flex flex-col gap-2 p-6">
                        <p className="text-sm text-stone-500">
                            {publishedCount} of {series.length} published
                        </p>
                        <div className="h-2 overflow-hidden rounded-full bg-stone-100">
                            <div
                                className="h-full rounded-full bg-mustard-500"
                                style={{
                                    width: `${(publishedCount / series.length) * 100}%`,
                                }}
                            />
                        </div>
                    </div>

                    <ol className="divide-y divide-stone-200 border-t border-stone-200">
                        {series.map((part) => (
                            <li
                                key={part.id}
                                className="flex flex-wrap items-center justify-between gap-4 px-6 py-4"
                            >
                                <Link
                                    href={route('admin.insights.edit', part.id)}
                                    className="min-w-0 font-semibold hover:text-mustard-700"
                                >
                                    Part {part.part}
                                    {part.title && `: ${part.title}`}
                                </Link>

                                <div className="flex items-center gap-4 text-sm font-medium">
                                    <InsightStatusBadge
                                        isPublished={part.is_published}
                                    />
                                    {part.is_published && (
                                        <a
                                            href={route(
                                                'insights.show',
                                                part.slug,
                                            )}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="text-stone-500 hover:text-stone-900"
                                        >
                                            View ↗
                                        </a>
                                    )}
                                </div>
                            </li>
                        ))}
                    </ol>
                </>
            )}
        </Panel>
    );
}

function Panel({ title, children }: { title: string; children: ReactNode }) {
    return (
        <section className="overflow-hidden border-y border-stone-200 bg-white text-stone-900 shadow-sm sm:rounded-2xl sm:border-x">
            <h3 className="border-b border-stone-200 px-6 py-4 font-semibold">
                {title}
            </h3>
            {children}
        </section>
    );
}
