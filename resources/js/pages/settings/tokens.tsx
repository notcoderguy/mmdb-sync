import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useState, useEffect } from 'react';
import HeadingSmall from '@/components/heading-small';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { TrashIcon, RefreshCwIcon } from 'lucide-react';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Token settings',
        href: '/settings/tokens',
    },
];

export default function Token() {
    const { tokens, flash } = usePage().props as unknown as { 
        tokens: Array<{ id: number; name: string; last_used_at: string; created_at: string }>,
        flash: {
            message?: string;
            error?: string;
            newToken?: { token: string; name: string };
        }
    };
    
    const [newToken, setNewToken] = useState<{ token: string; name: string } | null>(null);
    const { data, setData, post, delete: destroy, processing } = useForm({
        name: '',
        permissions: ['*'],
    });

    // Handle flash messages
    useEffect(() => {
        if (flash?.message) {
            toast.success(flash.message);
        }
        
        if (flash?.error) {
            toast.error(flash.error);
        }
        
        if (flash?.newToken) {
            setNewToken(flash.newToken);
            toast.success('Token created successfully! Make sure to copy it now.', {
                duration: 10000,
            });
        }
    }, [flash]);

    const createToken: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('tokens.store'));
    };

    const regenerateToken = (id: number) => {
        post(route('tokens.regenerate', id));
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
                                required
                            />
                        </div>
                        <Button type="submit" disabled={processing} className="bg-primary text-primary-foreground hover:bg-primary-dark">
                            Create Token
                        </Button>
                    </form>
                    
                    {/* Display New Token */}
                    {newToken && (
                        <div className="p-4 bg-success/10 border border-success rounded">
                            <p className="text-sm text-success">
                                Your new token: <span className="font-mono">{newToken.token}</span>
                            </p>
                            <p className="text-sm text-success">
                                Token Name: <span className="font-mono">{newToken.name}</span>
                            </p>
                            <p className="text-xs mt-2 text-success">
                                Make sure to copy your new token now. You won't be able to see it again!
                            </p>
                        </div>
                    )}
                    
                    {/* Token List */}
                    <div className="space-y-4">
                        <h3 className="text-lg font-medium">Existing Tokens</h3>
                        {tokens.length === 0 ? (
                            <p className="text-muted-foreground">No tokens created yet.</p>
                        ) : (
                            <table className="min-w-full border-collapse border border-muted">
                                <thead>
                                    <tr className="bg-muted/10">
                                        <th className="border border-muted px-4 py-2 text-left">Name</th>
                                        <th className="border border-muted px-4 py-2 text-left">Last Used At</th>
                                        <th className="border border-muted px-4 py-2 text-left">Created At</th>
                                        <th className="border border-muted px-4 py-2 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {tokens.map((token) => (
                                        <tr key={token.id} className="hover:bg-muted/5">
                                            <td className="border border-muted px-4 py-2">{token.name}</td>
                                            <td className="border border-muted px-4 py-2">
                                                {token.last_used_at
                                                    ? new Date(token.last_used_at).toLocaleString()
                                                    : 'Never'}
                                            </td>
                                            <td className="border border-muted px-4 py-2">
                                                {new Date(token.created_at).toLocaleString()}
                                            </td>
                                            <td className="border border-muted px-4 py-2 text-center space-x-2">
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    onClick={() => regenerateToken(token.id)}
                                                    title="Regenerate Token"
                                                >
                                                    <RefreshCwIcon className="h-5 w-5" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    onClick={() => deleteToken(token.id)}
                                                    title="Delete Token"
                                                >
                                                    <TrashIcon className="h-5 w-5" />
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}