import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Insight } from '@/types';
import { Head, Link, router } from '@inertiajs/react';

type InsightSummary = Pick<
    Insight,
    'id' | 'title' | 'slug' | 'published_at' | 'updated_at'
>;

export default function Index({ insights }: { insights: InsightSummary[] }) {
    const destroy = (insight: InsightSummary) => {
        if (confirm(`Delete "${insight.title}"? This can't be undone.`)) {
            router.delete(route('admin.insights.destroy', insight.id));
        }
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between gap-4">
                    <h2 className="text-3xl font-extrabold tracking-tight">
                        Insights<span className="text-mustard-500">.</span>
                    </h2>

                    <Link
                        href={route('admin.insights.create')}
                        className="inline-flex items-center rounded-md bg-stone-900 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-stone-700 focus:outline-none focus:ring-2 focus:ring-mustard-500 focus:ring-offset-2"
                    >
                        New insight
                    </Link>
                </div>
            }
        >
            <Head title="Insights" />

            <div className="pb-12 pt-2">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden border-y border-stone-200 bg-white shadow-sm sm:rounded-2xl sm:border-x">
                        {insights.length === 0 ? (
                            <p className="p-6 text-stone-600">
                                No insights yet. Write your first one!
                            </p>
                        ) : (
                            <ul className="divide-y divide-stone-200">
                                {insights.map((insight) => (
                                    <li
                                        key={insight.id}
                                        className="flex flex-wrap items-center justify-between gap-4 p-6"
                                    >
                                        <div className="min-w-0">
                                            <div className="flex items-center gap-3">
                                                <Link
                                                    href={route(
                                                        'admin.insights.edit',
                                                        insight.id,
                                                    )}
                                                    className="truncate font-semibold hover:text-mustard-700"
                                                >
                                                    {insight.title}
                                                </Link>
                                                <StatusBadge
                                                    isPublished={
                                                        insight.published_at !==
                                                        null
                                                    }
                                                />
                                            </div>
                                            <p className="mt-1 text-sm text-stone-500">
                                                Edited{' '}
                                                {new Date(
                                                    insight.updated_at,
                                                ).toLocaleDateString()}
                                            </p>
                                        </div>

                                        <div className="flex items-center gap-4 text-sm font-medium">
                                            {insight.published_at && (
                                                <a
                                                    href={route(
                                                        'insights.show',
                                                        insight.slug,
                                                    )}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    className="text-stone-500 hover:text-stone-900"
                                                >
                                                    View ↗
                                                </a>
                                            )}
                                            <Link
                                                href={route(
                                                    'admin.insights.edit',
                                                    insight.id,
                                                )}
                                                className="text-stone-500 hover:text-stone-900"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                type="button"
                                                onClick={() => destroy(insight)}
                                                className="text-red-600 hover:text-red-800"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

function StatusBadge({ isPublished }: { isPublished: boolean }) {
    return isPublished ? (
        <span className="rounded-full bg-mustard-100 px-2.5 py-0.5 text-xs font-medium text-mustard-800">
            Published
        </span>
    ) : (
        <span className="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-medium text-stone-600">
            Draft
        </span>
    );
}
