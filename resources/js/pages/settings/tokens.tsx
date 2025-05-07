import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';

import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useState } from 'react';

import HeadingSmall from '@/components/heading-small';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { TrashIcon, RefreshCwIcon, LockOpenIcon } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Token settings',
        href: '/settings/tokens',
    },
];

export default function Token() {
    const { tokens } = usePage().props as { tokens: Array<{ id: number; name: string; created_at: string }> };
    const [newToken, setNewToken] = useState<string | null>(null);

    const { data, setData, post, delete: destroy, processing } = useForm({
        name: '',
        permissions: ['*'], // Default permissions
    });

    const createToken: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('tokens.store'), {
            onSuccess: (response) => {
                setNewToken(response.props.token);
                setData('name', '');
            },
        });
    };

    const revokeToken = (id: number) => {
        destroy(route('tokens.revoke', id));
    };

    const regenerateToken = (id: number) => {
        post(route('tokens.regenerate', id), {
            onSuccess: (response) => {
                setNewToken(response.props.token);
            },
        });
    };

    const deleteToken = (id: number) => {
        destroy(route('tokens.destroy', id));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Token settings" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall
                        title="Personal Access Tokens"
                        description="Manage your personal access tokens"
                    />

                    {/* Token Creation Form */}
                    <form onSubmit={createToken} className="space-y-4">
                        <div className="grid gap-2">
                            <Label htmlFor="token_name">Token Name</Label>
                            <Input
                                id="token_name"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                type="text"
                                placeholder="Token name"
                                className="block w-full"
                            />
                        </div>
                        <Button type="submit" disabled={processing} className="bg-primary text-white hover:bg-primary-dark">
                            Create Token
                        </Button>
                    </form>

                    {/* Display New Token */}
                    {newToken && (
                        <div className="p-4 bg-success/10 border border-success rounded">
                            <p className="text-sm text-success">
                                Your new token: <span className="font-mono">{newToken}</span>
                            </p>
                        </div>
                    )}

                    {/* Token List */}
                    <div className="space-y-4">
                        <h3 className="text-lg font-medium">Existing Tokens</h3>
                        <table className="min-w-full border-collapse border border-muted">
                            <thead>
                                <tr className="bg-muted/10">
                                    <th className="border border-muted px-4 py-2 text-left">Name</th>
                                    <th className="border border-muted px-4 py-2 text-left">Created At</th>
                                    <th className="border border-muted px-4 py-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {tokens.map((token) => (
                                    <tr key={token.id} className="hover:bg-muted/5">
                                        <td className="border border-muted px-4 py-2">{token.name}</td>
                                        <td className="border border-muted px-4 py-2">
                                            {new Date(token.created_at).toLocaleString()}
                                        </td>
                                        <td className="border border-muted px-4 py-2 text-center space-x-2">
                                            <button
                                                onClick={() => regenerateToken(token.id)}
                                                className="text-primary hover:text-primary-dark"
                                                title="Regenerate Token"
                                            >
                                                <RefreshCwIcon className="h-5 w-5 inline" />
                                            </button>
                                            <button
                                                onClick={() => revokeToken(token.id)}
                                                className="text-warning hover:text-warning-dark"
                                                title="Revoke Token"
                                            >
                                                <LockOpenIcon className="h-5 w-5 inline" />
                                            </button>
                                            <button
                                                onClick={() => deleteToken(token.id)}
                                                className="text-danger hover:text-danger-dark"
                                                title="Delete Token"
                                            >
                                                <TrashIcon className="h-5 w-5 inline" />
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}