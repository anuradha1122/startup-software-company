import { Head, Link, router, useForm } from '@inertiajs/react';

type PhaseForm = {
    name: string;
    description: string;
    planned_start_date: string;
    planned_end_date: string;
};

type MilestoneForm = {
    project_phase_id: string;
    name: string;
    description: string;
    due_date: string;
    client_visible: boolean;
};

type RiskForm = {
    title: string;
    description: string;
    category: string;
    likelihood: number;
    impact: number;
    owner_id: string;
    mitigation: string;
    contingency: string;
    review_date: string;
};

type DecisionForm = {
    title: string;
    decision: string;
    rationale: string;
    decided_at: string;
    client_visible: boolean;
};

export default function ProjectShow({ project, users }: any) {
    const charter = project.charters?.[0];

    const phaseForm = useForm<PhaseForm>({
        name: '',
        description: '',
        planned_start_date: '',
        planned_end_date: '',
    });

    const milestoneForm = useForm<MilestoneForm>({
        project_phase_id: '',
        name: '',
        description: '',
        due_date: '',
        client_visible: true,
    });

    const riskForm = useForm<RiskForm>({
        title: '',
        description: '',
        category: '',
        likelihood: 1,
        impact: 1,
        owner_id: '',
        mitigation: '',
        contingency: '',
        review_date: '',
    });

    const decisionForm = useForm<DecisionForm>({
        title: '',
        decision: '',
        rationale: '',
        decided_at: new Date().toISOString().slice(0, 10),

        client_visible: false,
    });

    return (
        <>
            <Head title={project.project_number} />

            <div className="max-w-7xl space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {project.project_number}
                        </h1>

                        <p className="text-lg">{project.name}</p>

                        <p className="text-sm text-muted-foreground">
                            {project.client?.name}
                            {' · '}
                            {project.status}
                            {' · '}
                            Health: {project.health}
                        </p>
                    </div>

                    <div className="flex flex-wrap gap-2">
                        {project.approved_charter && (
                            <a
                                href={`/admin/projects/${project.public_id}/charter-pdf`}
                                className="rounded border px-3 py-2"
                            >
                                Charter PDF
                            </a>
                        )}

                        {project.status === 'planned' &&
                            project.approved_charter_id && (
                                <button
                                    onClick={() =>
                                        router.post(
                                            `/admin/projects/${project.public_id}/activate`,
                                        )
                                    }
                                    className="rounded bg-primary px-3 py-2 text-primary-foreground"
                                >
                                    Activate
                                </button>
                            )}

                        {project.status === 'active' && (
                            <>
                                <button
                                    onClick={() =>
                                        router.post(
                                            `/admin/projects/${project.public_id}/kickoff`,
                                        )
                                    }
                                    className="rounded border px-3 py-2"
                                >
                                    Send Kickoff
                                </button>

                                <button
                                    onClick={() =>
                                        router.post(
                                            `/admin/projects/${project.public_id}/hold`,
                                        )
                                    }
                                    className="rounded border px-3 py-2"
                                >
                                    Hold
                                </button>
                            </>
                        )}

                        {project.status === 'on_hold' && (
                            <button
                                onClick={() =>
                                    router.post(
                                        `/admin/projects/${project.public_id}/resume`,
                                    )
                                }
                                className="rounded border px-3 py-2"
                            >
                                Resume
                            </button>
                        )}

                        {['active', 'on_hold'].includes(project.status) && (
                            <Link
                                href={`/admin/projects/${project.public_id}/tasks/board`}
                                className="rounded border px-3 py-2"
                            >
                                Task Board
                            </Link>
                        )}

                        <Link
                            href={`/admin/projects/${project.public_id}/team`}
                            className="rounded border px-3 py-2"
                        >
                            Project Team
                        </Link>

                        <Link
                            href={`/admin/projects/${project.public_id}/internal-costs`}
                            className="rounded border px-3 py-2"
                        >
                            Internal Costs
                        </Link>

                        <Link
                            href={`/admin/projects/${project.public_id}/expenses`}
                            className="rounded border px-3 py-2"
                        >
                            Expenses
                        </Link>

                        <Link
                            href={`/admin/projects/${project.public_id}/non-labor-costs`}
                            className="rounded border px-3 py-2"
                        >
                            Non-Labor Cost
                        </Link>
                    </div>
                </div>

                <section className="grid gap-4 rounded border p-5 md:grid-cols-4">
                    <div>
                        <span className="text-sm text-muted-foreground">
                            Contract
                        </span>

                        <p>{project.contract?.contract_number}</p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Project Manager
                        </span>

                        <p>{project.project_manager?.name}</p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Contract Value
                        </span>

                        <p>
                            {project.currency} {project.contract_value}
                        </p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Requirement
                        </span>

                        <p>
                            {
                                project.requirement_version?.requirement
                                    ?.requirement_number
                            }
                        </p>
                    </div>
                </section>

                <section className="rounded border p-5">
                    <div className="mb-4 flex justify-between">
                        <h2 className="text-lg font-semibold">
                            Project Charter
                        </h2>

                        {charter && (
                            <div className="flex gap-2">
                                {charter.status === 'draft' && (
                                    <>
                                        <Link
                                            href={`/admin/projects/${project.public_id}/charters/${charter.public_id}/edit`}
                                            className="rounded border px-3 py-2"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            onClick={() =>
                                                router.post(
                                                    `/admin/projects/${project.public_id}/charters/${charter.public_id}/approve`,
                                                )
                                            }
                                            className="rounded border px-3 py-2"
                                        >
                                            Approve
                                        </button>
                                    </>
                                )}
                            </div>
                        )}
                    </div>

                    <p>
                        Version {charter?.version_number}
                        {' · '}
                        {charter?.status}
                    </p>
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">Phases</h2>

                    <form
                        className="mb-5 grid gap-3 md:grid-cols-4"
                        onSubmit={(event) => {
                            event.preventDefault();

                            phaseForm.post(
                                `/admin/projects/${project.public_id}/phases`,
                                {
                                    preserveScroll: true,
                                },
                            );
                        }}
                    >
                        <input
                            className="rounded border p-2"
                            placeholder="Phase name"
                            value={phaseForm.data.name}
                            onChange={(e) =>
                                phaseForm.setData('name', e.target.value)
                            }
                        />

                        <input
                            type="date"
                            className="rounded border p-2"
                            value={phaseForm.data.planned_start_date}
                            onChange={(e) =>
                                phaseForm.setData(
                                    'planned_start_date',
                                    e.target.value,
                                )
                            }
                        />

                        <input
                            type="date"
                            className="rounded border p-2"
                            value={phaseForm.data.planned_end_date}
                            onChange={(e) =>
                                phaseForm.setData(
                                    'planned_end_date',
                                    e.target.value,
                                )
                            }
                        />

                        <button className="rounded border px-3 py-2">
                            Add Phase
                        </button>
                    </form>

                    {project.phases.map((phase: any) => (
                        <div
                            key={phase.public_id}
                            className="mb-2 rounded border p-3"
                        >
                            <strong>{phase.name}</strong>

                            <p className="text-sm text-muted-foreground">
                                {phase.status}
                            </p>
                        </div>
                    ))}
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">Milestones</h2>

                    <form
                        className="mb-5 grid gap-3 md:grid-cols-4"
                        onSubmit={(event) => {
                            event.preventDefault();

                            milestoneForm.post(
                                `/admin/projects/${project.public_id}/milestones`,
                                {
                                    preserveScroll: true,
                                },
                            );
                        }}
                    >
                        <select
                            className="rounded border p-2"
                            value={milestoneForm.data.project_phase_id}
                            onChange={(e) =>
                                milestoneForm.setData(
                                    'project_phase_id',
                                    e.target.value,
                                )
                            }
                        >
                            <option value="">No phase</option>

                            {project.phases.map((phase: any) => (
                                <option key={phase.id} value={phase.id}>
                                    {phase.name}
                                </option>
                            ))}
                        </select>

                        <input
                            className="rounded border p-2"
                            placeholder="Milestone"
                            value={milestoneForm.data.name}
                            onChange={(e) =>
                                milestoneForm.setData('name', e.target.value)
                            }
                        />

                        <input
                            type="date"
                            className="rounded border p-2"
                            value={milestoneForm.data.due_date}
                            onChange={(e) =>
                                milestoneForm.setData(
                                    'due_date',
                                    e.target.value,
                                )
                            }
                        />

                        <button className="rounded border px-3 py-2">
                            Add Milestone
                        </button>
                    </form>

                    {project.milestones.map((milestone: any) => (
                        <div
                            key={milestone.public_id}
                            className="mb-2 flex justify-between rounded border p-3"
                        >
                            <div>
                                <strong>{milestone.name}</strong>

                                <p>{milestone.due_date}</p>
                            </div>

                            <span>{milestone.status}</span>
                        </div>
                    ))}
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Risk Register
                    </h2>

                    <form
                        className="mb-6 grid gap-3 md:grid-cols-3"
                        onSubmit={(event) => {
                            event.preventDefault();

                            riskForm.post(
                                `/admin/projects/${project.public_id}/risks`,
                                {
                                    preserveScroll: true,
                                },
                            );
                        }}
                    >
                        <input
                            className="rounded border p-2"
                            placeholder="Risk title"
                            value={riskForm.data.title}
                            onChange={(e) =>
                                riskForm.setData('title', e.target.value)
                            }
                        />

                        <input
                            className="rounded border p-2"
                            placeholder="Category"
                            value={riskForm.data.category}
                            onChange={(e) =>
                                riskForm.setData('category', e.target.value)
                            }
                        />

                        <select
                            className="rounded border p-2"
                            value={riskForm.data.owner_id}
                            onChange={(e) =>
                                riskForm.setData('owner_id', e.target.value)
                            }
                        >
                            <option value="">Risk Owner</option>

                            {users.map((user: any) => (
                                <option key={user.id} value={user.id}>
                                    {user.name}
                                </option>
                            ))}
                        </select>

                        <input
                            type="number"
                            min="1"
                            max="5"
                            className="rounded border p-2"
                            value={riskForm.data.likelihood}
                            onChange={(e) =>
                                riskForm.setData(
                                    'likelihood',
                                    Number(e.target.value),
                                )
                            }
                        />

                        <input
                            type="number"
                            min="1"
                            max="5"
                            className="rounded border p-2"
                            value={riskForm.data.impact}
                            onChange={(e) =>
                                riskForm.setData(
                                    'impact',
                                    Number(e.target.value),
                                )
                            }
                        />

                        <button className="rounded border px-3 py-2">
                            Add Risk
                        </button>
                    </form>

                    {project.risks.map((risk: any) => (
                        <div
                            key={risk.public_id}
                            className="mb-3 flex justify-between rounded border p-4"
                        >
                            <div>
                                <strong>
                                    {risk.risk_number}
                                    {' — '}
                                    {risk.title}
                                </strong>

                                <p>
                                    Score: {risk.score}
                                    {' · '}
                                    {risk.status}
                                </p>
                            </div>

                            {risk.status !== 'closed' && (
                                <button
                                    onClick={() =>
                                        router.post(
                                            `/admin/projects/${project.public_id}/risks/${risk.public_id}/escalate`,
                                        )
                                    }
                                    className="rounded border px-3 py-2"
                                >
                                    Escalate
                                </button>
                            )}
                        </div>
                    ))}
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Governance Decisions
                    </h2>

                    <form
                        className="mb-6 space-y-3"
                        onSubmit={(event) => {
                            event.preventDefault();

                            decisionForm.post(
                                `/admin/projects/${project.public_id}/decisions`,
                                {
                                    preserveScroll: true,
                                },
                            );
                        }}
                    >
                        <input
                            className="w-full rounded border p-2"
                            placeholder="Decision title"
                            value={decisionForm.data.title}
                            onChange={(e) =>
                                decisionForm.setData('title', e.target.value)
                            }
                        />

                        <textarea
                            className="min-h-24 w-full rounded border p-2"
                            placeholder="Decision"
                            value={decisionForm.data.decision}
                            onChange={(e) =>
                                decisionForm.setData('decision', e.target.value)
                            }
                        />

                        <button className="rounded border px-3 py-2">
                            Record Decision
                        </button>
                    </form>

                    {project.decisions.map((decision: any) => (
                        <div
                            key={decision.public_id}
                            className="mb-3 rounded border p-3"
                        >
                            <strong>{decision.title}</strong>

                            <p className="whitespace-pre-wrap">
                                {decision.decision}
                            </p>
                        </div>
                    ))}
                </section>
            </div>
        </>
    );
}
