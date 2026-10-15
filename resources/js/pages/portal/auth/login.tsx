import { Head, Link, useForm } from '@inertiajs/react';

type LoginForm = {
    email: string;
    password: string;
    remember: boolean;
};

export default function PortalLogin() {
    const form = useForm<LoginForm>({
        email: '',
        password: '',
        remember: false,
    });

    return (
        <>
            <Head title="Client Portal Login" />

            <div className="flex min-h-screen items-center justify-center bg-muted p-6">
                <form
                    className="w-full max-w-md space-y-5 rounded-lg border bg-background p-6"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post('/portal/login');
                    }}
                >
                    <div>
                        <h1 className="text-2xl font-semibold">
                            Client Portal
                        </h1>

                        <p className="text-sm text-muted-foreground">
                            Sign in to view your projects, approvals and
                            invoices.
                        </p>
                    </div>

                    <label className="block space-y-1">
                        <span className="text-sm">Email</span>

                        <input
                            type="email"
                            autoComplete="email"
                            value={form.data.email}
                            onChange={(event) =>
                                form.setData('email', event.target.value)
                            }
                            className="w-full rounded border p-2"
                        />

                        {form.errors.email && (
                            <p className="text-sm text-destructive">
                                {form.errors.email}
                            </p>
                        )}
                    </label>

                    <label className="block space-y-1">
                        <span className="text-sm">Password</span>

                        <input
                            type="password"
                            autoComplete="current-password"
                            value={form.data.password}
                            onChange={(event) =>
                                form.setData('password', event.target.value)
                            }
                            className="w-full rounded border p-2"
                        />
                    </label>

                    <label className="flex items-center gap-2">
                        <input
                            type="checkbox"
                            checked={form.data.remember}
                            onChange={(event) =>
                                form.setData('remember', event.target.checked)
                            }
                        />
                        Remember me
                    </label>

                    <button
                        disabled={form.processing}
                        className="w-full rounded bg-primary px-4 py-2 text-primary-foreground disabled:opacity-50"
                    >
                        Sign In
                    </button>

                    <Link
                        href="/portal/forgot-password"
                        className="block text-center text-sm underline"
                    >
                        Forgot password?
                    </Link>
                </form>
            </div>
        </>
    );
}
