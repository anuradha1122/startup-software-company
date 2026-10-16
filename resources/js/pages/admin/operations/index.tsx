import { Head, router } from '@inertiajs/react';

type HealthCheck = {
    key: string;
    label: string;
    status: 'healthy' | 'warning' | 'critical';
    value: string | number | null;
    message: string;
};

type Health = {
    status: 'healthy' | 'warning' | 'critical';
    checked_at: string;
    issue_count: number;
    critical_count: number;
    environment: string;
    queue_connection: string;
    checks: HealthCheck[];
};

type Props = {
    health: Health;
    canRunHealthCheck: boolean;
};

export default function AdminOperations({ health, canRunHealthCheck }: Props) {
    return (
        <>
            <Head title="Admin Operations" />

            <div className="space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            Admin Operations
                        </h1>

                        <p className="text-sm text-muted-foreground">
                            Application operational health and administrative
                            diagnostics.
                        </p>
                    </div>

                    {canRunHealthCheck && (
                        <button
                            type="button"
                            onClick={() =>
                                router.post('/admin/operations/health-check')
                            }
                            className="rounded border px-4 py-2"
                        >
                            Run Health Check
                        </button>
                    )}
                </div>

                <section className="grid gap-4 md:grid-cols-4">
                    <Metric label="Overall Status" value={health.status} />

                    <Metric label="Issues" value={String(health.issue_count)} />

                    <Metric
                        label="Critical"
                        value={String(health.critical_count)}
                    />

                    <Metric label="Queue" value={health.queue_connection} />
                </section>

                <section className="rounded border p-5">
                    <div className="mb-4">
                        <h2 className="text-lg font-semibold">Health Checks</h2>

                        <p className="text-sm text-muted-foreground">
                            Checked: {health.checked_at}
                            {' · '}
                            Environment: {health.environment}
                        </p>
                    </div>

                    <div className="space-y-3">
                        {health.checks.map((check) => (
                            <div key={check.key} className="rounded border p-4">
                                <div className="flex flex-wrap justify-between gap-3">
                                    <div>
                                        <h3 className="font-medium">
                                            {check.label}
                                        </h3>

                                        <p className="mt-1 text-sm text-muted-foreground">
                                            {check.message}
                                        </p>
                                    </div>

                                    <div className="text-right">
                                        <p className="font-medium">
                                            {check.status}
                                        </p>

                                        <p className="text-sm text-muted-foreground">
                                            {check.value ?? '-'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </section>
            </div>
        </>
    );
}

function Metric({ label, value }: { label: string; value: string }) {
    return (
        <div className="rounded border p-4">
            <p className="text-sm text-muted-foreground">{label}</p>

            <p className="mt-1 text-xl font-semibold">{value}</p>
        </div>
    );
}
