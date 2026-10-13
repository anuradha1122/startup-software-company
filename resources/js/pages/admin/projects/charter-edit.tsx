import { Head, useForm } from '@inertiajs/react';

type CharterForm = {
    business_objective: string;
    project_objective: string;
    governance_structure: string;
    communication_plan: string;
    escalation_process: string;
    success_criteria: string[];
    assumptions: string[];
    constraints: string[];
};

function ListField({
    label,
    values,
    onChange,
}: {
    label: string;
    values: string[];
    onChange: (values: string[]) => void;
}) {
    return (
        <div className="space-y-2">
            <div className="flex justify-between">
                <h3 className="font-medium">{label}</h3>

                <button
                    type="button"
                    className="rounded border px-2 py-1"
                    onClick={() => onChange([...values, ''])}
                >
                    Add
                </button>
            </div>

            {values.map((value: string, index: number) => (
                <div key={index} className="flex gap-2">
                    <input
                        className="flex-1 rounded border p-2"
                        value={value}
                        onChange={(event) => {
                            const next = [...values];

                            next[index] = event.target.value;

                            onChange(next);
                        }}
                    />

                    <button
                        type="button"
                        onClick={() =>
                            onChange(
                                values.filter(
                                    (_: string, i: number) => i !== index,
                                ),
                            )
                        }
                    >
                        Remove
                    </button>
                </div>
            ))}
        </div>
    );
}

export default function CharterEdit({ project, charter }: any) {
    const form = useForm<CharterForm>({
        business_objective: charter.business_objective ?? '',

        project_objective: charter.project_objective ?? '',

        governance_structure: charter.governance_structure ?? '',

        communication_plan: charter.communication_plan ?? '',

        escalation_process: charter.escalation_process ?? '',

        success_criteria: charter.success_criteria?.length
            ? charter.success_criteria
            : [''],

        assumptions: charter.assumptions?.length ? charter.assumptions : [''],

        constraints: charter.constraints?.length ? charter.constraints : [''],
    });

    return (
        <>
            <Head title="Project Charter" />

            <div className="max-w-5xl space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">Project Charter</h1>

                    <p>
                        {project.project_number}
                        {' — '}
                        {project.name}
                    </p>
                </div>

                <form
                    className="space-y-6"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.put(
                            `/admin/projects/${project.public_id}/charters/${charter.public_id}`,
                        );
                    }}
                >
                    {(
                        [
                            ['business_objective', 'Business Objective'],
                            ['project_objective', 'Project Objective'],
                            ['governance_structure', 'Governance Structure'],
                            ['communication_plan', 'Communication Plan'],
                            ['escalation_process', 'Escalation Process'],
                        ] as const
                    ).map(([field, label]) => (
                        <label key={field} className="block space-y-1">
                            <span>{label}</span>

                            <textarea
                                className="min-h-32 w-full rounded border p-2"
                                value={form.data[field]}
                                onChange={(e) =>
                                    form.setData(field, e.target.value)
                                }
                            />
                        </label>
                    ))}

                    <ListField
                        label="Success Criteria"
                        values={form.data.success_criteria}
                        onChange={(value) =>
                            form.setData('success_criteria', value)
                        }
                    />

                    <ListField
                        label="Assumptions"
                        values={form.data.assumptions}
                        onChange={(value) => form.setData('assumptions', value)}
                    />

                    <ListField
                        label="Constraints"
                        values={form.data.constraints}
                        onChange={(value) => form.setData('constraints', value)}
                    />

                    <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                        Save Charter
                    </button>
                </form>
            </div>
        </>
    );
}
