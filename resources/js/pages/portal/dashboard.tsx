import { Head, Link, router } from '@inertiajs/react';

type PortalProject = {
    public_id: string;
    project_number: string;
    name: string;
    status: string;
};

type Props = {
    portalUser: {
        name: string;
        email: string;
        client_name: string;
    };
    projects: PortalProject[];
    pendingApprovalCount: number;
    outstandingInvoiceCount: number;
};

export default function PortalDashboard({
    portalUser,
    projects,
    pendingApprovalCount,
    outstandingInvoiceCount,
}: Props) {
    return (
        <>
            <Head title="Client Portal" />

            <div className="min-h-screen bg-muted/30">
                <header className="border-b bg-background">
                    <div className="mx-auto flex max-w-7xl items-center justify-between p-5">
                        <div>
                            <h1 className="text-xl font-semibold">
                                Client Portal
                            </h1>

                            <p className="text-sm text-muted-foreground">
                                {portalUser.client_name}
                            </p>
                        </div>

                        <button
                            type="button"
                            onClick={() => router.post('/portal/logout')}
                            className="rounded border px-3 py-2"
                        >
                            Sign Out
                        </button>
                    </div>
                </header>

                <main className="mx-auto max-w-7xl space-y-6 p-6">
                    <div>
                        <h2 className="text-2xl font-semibold">
                            Welcome, {portalUser.name}
                        </h2>
                    </div>

                    <section className="grid gap-4 md:grid-cols-3">
                        <div className="rounded border bg-background p-5">
                            <p className="text-sm text-muted-foreground">
                                Projects
                            </p>

                            <p className="text-2xl font-semibold">
                                {projects.length}
                            </p>
                        </div>

                        <Link
                            href="/portal/approvals"
                            className="rounded border bg-background p-5"
                        >
                            <p className="text-sm text-muted-foreground">
                                Pending Approvals
                            </p>

                            <p className="text-2xl font-semibold">
                                {pendingApprovalCount}
                            </p>
                        </Link>

                        <Link
                            href="/portal/invoices"
                            className="rounded border bg-background p-5"
                        >
                            <p className="text-sm text-muted-foreground">
                                Outstanding Invoices
                            </p>

                            <p className="text-2xl font-semibold">
                                {outstandingInvoiceCount}
                            </p>
                        </Link>
                    </section>

                    <section className="rounded border bg-background p-5">
                        <h2 className="mb-4 text-lg font-semibold">Projects</h2>

                        <div className="space-y-3">
                            {projects.map((project) => (
                                <Link
                                    key={project.public_id}
                                    href={`/portal/projects/${project.public_id}`}
                                    className="block rounded border p-4"
                                >
                                    <p className="font-medium">
                                        {project.project_number}
                                        {' — '}
                                        {project.name}
                                    </p>

                                    <p className="text-sm text-muted-foreground">
                                        Status: {project.status}
                                    </p>
                                </Link>
                            ))}
                        </div>
                    </section>
                </main>
            </div>
        </>
    );
}
