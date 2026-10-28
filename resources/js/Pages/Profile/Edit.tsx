import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';

export default function Edit() {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-3xl font-extrabold tracking-tight">
                    Profile<span className="text-mustard-500">.</span>
                </h2>
            }
        >
            <Head title="Profile" />

            <div className="pb-12 pt-2">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="border-y border-stone-200 bg-white p-4 shadow-sm sm:rounded-2xl sm:border-x sm:p-8">
                        <UpdateProfileInformationForm className="max-w-xl" />
                    </div>

                    <div className="border-y border-stone-200 bg-white p-4 shadow-sm sm:rounded-2xl sm:border-x sm:p-8">
                        <UpdatePasswordForm className="max-w-xl" />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
