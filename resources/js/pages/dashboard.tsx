import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

type PageProps = {
    downloadLinks: {
        type: string;
        label: string;
        url: string | null;
    }[];
    lastModifiedDates: { [key: string]: string | null };
};

export default function Dashboard(props: PageProps) {
    const { post, processing } = useForm();

    const handleUpdate = () => {
        post('/dashboard/update', {
            onSuccess: () => {
                alert('Databases updated successfully!');
            },
            onError: () => {
                alert('Failed to update databases.');
            },
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    {props.downloadLinks.map((link) => (
                        <div
                            key={link.type}
                            className="border-sidebar-border/70 dark:border-sidebar-border relative flex aspect-video flex-col items-center justify-center overflow-hidden rounded-xl border p-4"
                        >
                            <a
                                href={link.url || '#'}
                                className="decoration-sidebar-primary/70 hover:decoration-sidebar-primary/100 dark:decoration-sidebar-primary/70 dark:hover:decoration-sidebar-primary/100 text-center text-lg font-semibold underline decoration-dashed transition-all duration-200"
                                target="_blank"
                                rel="noopener noreferrer"
                                onClick={e => !link.url && e.preventDefault()}
                            >
                                {link.label}
                            </a>
                            <p className="mt-2 text-sm text-gray-500">Last Modified: {props.lastModifiedDates[link.type] || 'Not Available'}</p>
                        </div>
                    ))}
                </div>
                <div className="border-sidebar-border/70 dark:border-sidebar-border relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border md:min-h-min">
                    <div className="absolute inset-0 flex flex-col items-center justify-center overflow-hidden rounded-xl text-center text-4xl font-bold">
                        MMDB-SYNC
                        <div className="mt-4 flex justify-end text-sm">
                            <button
                                onClick={handleUpdate}
                                className="bg-primary text-primary-foreground hover:bg-primary/90 disabled:bg-primary/50 flex items-center gap-2 rounded px-4 py-2 transition-all duration-200 hover:cursor-pointer"
                                disabled={processing}
                            >
                                {processing ? 'Updating...' : 'Update Databases'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
