import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard() {
    const downloadLinks = [
        { type: 'asn', label: 'ASN Database' },
        { type: 'country', label: 'Country Database' },
        { type: 'city', label: 'City Database' },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    {downloadLinks.map((link) => (
                        <div
                            key={link.type}
                            className="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border flex items-center justify-center"
                        >
                            <a
                                href={`/api/mmdb/download/${link.type}`}
                                className="text-center text-lg font-semibold underline decoration-dashed decoration-sidebar-primary/70 transition-all duration-200 hover:decoration-sidebar-primary/100 dark:decoration-sidebar-primary/70 dark:hover:decoration-sidebar-primary/100"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                {link.label}
                            </a>
                        </div>
                    ))}
                </div>
                <div className="border-sidebar-border/70 dark:border-sidebar-border relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border md:min-h-min">
                    <div className="absolute inset-0 flex items-center justify-center overflow-hidden rounded-xl text-center text-4xl font-bold">
                        MMDB-SYNC
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
