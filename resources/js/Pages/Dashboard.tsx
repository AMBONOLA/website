import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard() {
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
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden border-y border-stone-200 bg-white shadow-sm sm:rounded-2xl sm:border-x">
                        <div className="p-6 text-stone-900">
                            You're logged in!
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
