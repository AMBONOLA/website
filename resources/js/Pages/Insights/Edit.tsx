import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Insight } from '@/types';
import { Transition } from '@headlessui/react';
import { Head, Link, useForm } from '@inertiajs/react';
import axios from 'axios';
import { FormEventHandler, useState } from 'react';

const textareaClassName =
    'mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-mustard-500 focus:ring-mustard-500';

function slugify(text: string): string {
    return text
        .normalize('NFD')
        .replace(/[̀-ͯ]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

export default function Edit({ insight }: { insight: Insight | null }) {
    const { data, setData, post, put, errors, processing, recentlySuccessful } =
        useForm({
            title: insight?.title ?? '',
            slug: insight?.slug ?? '',
            excerpt: insight?.excerpt ?? '',
            body: insight?.body ?? '',
            is_published: insight?.published_at != null,
        });

    const [isSlugEdited, setIsSlugEdited] = useState(insight !== null);
    const [tab, setTab] = useState<'write' | 'preview'>('write');
    const [previewHtml, setPreviewHtml] = useState('');
    const [isPreviewLoading, setIsPreviewLoading] = useState(false);

    const showPreview = () => {
        setTab('preview');
        setIsPreviewLoading(true);

        axios
            .post(route('admin.insights.preview'), { body: data.body })
            .then((response) => setPreviewHtml(response.data.html))
            .finally(() => setIsPreviewLoading(false));
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (insight) {
            put(route('admin.insights.update', insight.id), {
                preserveScroll: true,
            });
        } else {
            post(route('admin.insights.store'));
        }
    };

    const tabClassName = (name: 'write' | 'preview') =>
        'border-b-2 px-1 pb-2 text-sm font-medium transition ' +
        (tab === name
            ? 'border-mustard-500 text-stone-900'
            : 'border-transparent text-stone-500 hover:text-stone-700');

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between gap-4">
                    <h2 className="text-3xl font-extrabold tracking-tight">
                        {insight ? 'Edit insight' : 'New insight'}
                        <span className="text-mustard-500">.</span>
                    </h2>

                    <Link
                        href={route('admin.insights.index')}
                        className="text-sm font-medium text-stone-500 hover:text-stone-900"
                    >
                        ← All insights
                    </Link>
                </div>
            }
        >
            <Head title={insight ? `Edit: ${insight.title}` : 'New insight'} />

            <div className="pb-12 pt-2">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <form
                        onSubmit={submit}
                        className="space-y-6 border-y border-stone-200 bg-white p-4 shadow-sm sm:rounded-2xl sm:border-x sm:p-8"
                    >
                        <div>
                            <InputLabel htmlFor="title" value="Title" />
                            <TextInput
                                id="title"
                                className="mt-1 block w-full"
                                value={data.title}
                                onChange={(e) => {
                                    setData((previous) => ({
                                        ...previous,
                                        title: e.target.value,
                                        slug: isSlugEdited
                                            ? previous.slug
                                            : slugify(e.target.value),
                                    }));
                                }}
                                isFocused={insight === null}
                                required
                            />
                            <InputError
                                className="mt-2"
                                message={errors.title}
                            />
                        </div>

                        <div>
                            <InputLabel htmlFor="slug" value="URL" />
                            <div className="mt-1 flex items-center rounded-md shadow-sm">
                                <span className="rounded-l-md border border-r-0 border-stone-300 bg-stone-50 px-3 py-2 text-sm text-stone-500">
                                    /insights/
                                </span>
                                <TextInput
                                    id="slug"
                                    className="block w-full rounded-l-none shadow-none"
                                    value={data.slug}
                                    onChange={(e) => {
                                        setIsSlugEdited(true);
                                        setData('slug', e.target.value);
                                    }}
                                />
                            </div>
                            <InputError
                                className="mt-2"
                                message={errors.slug}
                            />
                        </div>

                        <div>
                            <InputLabel htmlFor="excerpt" value="Excerpt" />
                            <textarea
                                id="excerpt"
                                rows={2}
                                maxLength={300}
                                className={textareaClassName}
                                value={data.excerpt}
                                onChange={(e) =>
                                    setData('excerpt', e.target.value)
                                }
                                required
                            />
                            <p className="mt-1 text-sm text-stone-500">
                                Shown on the insight cards and in search
                                results. {data.excerpt.length}/300
                            </p>
                            <InputError
                                className="mt-2"
                                message={errors.excerpt}
                            />
                        </div>

                        <div>
                            <div className="flex items-end justify-between gap-4">
                                <InputLabel htmlFor="body" value="Body" />
                                <div className="flex gap-4" role="tablist">
                                    <button
                                        type="button"
                                        role="tab"
                                        aria-selected={tab === 'write'}
                                        className={tabClassName('write')}
                                        onClick={() => setTab('write')}
                                    >
                                        Write
                                    </button>
                                    <button
                                        type="button"
                                        role="tab"
                                        aria-selected={tab === 'preview'}
                                        className={tabClassName('preview')}
                                        onClick={showPreview}
                                    >
                                        Preview
                                    </button>
                                </div>
                            </div>

                            {tab === 'write' ? (
                                <textarea
                                    id="body"
                                    rows={20}
                                    className={
                                        textareaClassName + ' font-mono text-sm'
                                    }
                                    value={data.body}
                                    onChange={(e) =>
                                        setData('body', e.target.value)
                                    }
                                    placeholder={
                                        '## A heading\n\nWrite in **Markdown**. Use `code`, lists, links and\n\n```\ncode blocks\n```'
                                    }
                                    required
                                />
                            ) : (
                                <div className="mt-1 min-h-[30rem] rounded-md border border-stone-300 p-6">
                                    {isPreviewLoading ? (
                                        <div className="animate-pulse space-y-3">
                                            <div className="h-4 w-3/4 rounded bg-stone-200" />
                                            <div className="h-4 w-1/2 rounded bg-stone-200" />
                                            <div className="h-4 w-2/3 rounded bg-stone-200" />
                                        </div>
                                    ) : (
                                        // The server escapes raw HTML in the Markdown, so this is safe to inject.
                                        <div
                                            className="insight-body"
                                            dangerouslySetInnerHTML={{
                                                __html:
                                                    previewHtml ||
                                                    '<p>Nothing to preview yet.</p>',
                                            }}
                                        />
                                    )}
                                </div>
                            )}
                            <InputError
                                className="mt-2"
                                message={errors.body}
                            />
                        </div>

                        <div>
                            <label className="flex items-center">
                                <Checkbox
                                    checked={data.is_published}
                                    onChange={(e) =>
                                        setData(
                                            'is_published',
                                            e.target.checked,
                                        )
                                    }
                                />
                                <span className="ms-2 text-sm text-stone-700">
                                    Published (visible on the public site)
                                </span>
                            </label>
                            <InputError
                                className="mt-2"
                                message={errors.is_published}
                            />
                        </div>

                        <div className="flex items-center gap-4">
                            <PrimaryButton disabled={processing}>
                                {insight ? 'Save' : 'Create'}
                            </PrimaryButton>

                            {insight?.published_at && (
                                <a
                                    href={route('insights.show', insight.slug)}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="text-sm font-medium text-stone-500 hover:text-stone-900"
                                >
                                    View live ↗
                                </a>
                            )}

                            <Transition
                                show={recentlySuccessful}
                                enter="transition ease-in-out"
                                enterFrom="opacity-0"
                                leave="transition ease-in-out"
                                leaveTo="opacity-0"
                            >
                                <p className="text-sm text-stone-600">Saved.</p>
                            </Transition>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
