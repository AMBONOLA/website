import { PropsWithChildren } from 'react';

export default function Guest({ children }: PropsWithChildren) {
    return (
        <div className="flex min-h-screen flex-col bg-stone-50 text-stone-900 selection:bg-mustard-300">
            <header className="mx-auto flex w-full max-w-4xl items-center px-6 py-8">
                <a
                    href={route('home')}
                    className="text-lg font-extrabold tracking-tight"
                >
                    AB<span className="text-mustard-500">.</span>
                </a>
            </header>

            <main className="flex flex-1 items-center justify-center px-6 pb-16 sm:pb-24">
                <div className="w-full max-w-md rounded-2xl border border-stone-200 bg-white p-8 shadow-sm">
                    {children}
                </div>
            </main>

            <footer className="border-t border-stone-200">
                <div className="mx-auto w-full max-w-4xl px-6 py-6 text-sm text-stone-500">
                    &copy; {new Date().getFullYear()} Andrea Bonola
                </div>
            </footer>
        </div>
    );
}
