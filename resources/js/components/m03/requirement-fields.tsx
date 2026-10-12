function Lines({
    title,
    values,
    onChange,
}: {
    title: string;
    values: string[];
    onChange: (values: string[]) => void;
}) {
    const update = (index: number, value: string) => {
        const next = [...values];

        next[index] = value;

        onChange(next);
    };

    return (
        <div className="space-y-2">
            <div className="flex items-center justify-between">
                <h3 className="font-medium">{title}</h3>

                <button
                    type="button"
                    className="rounded border px-2 py-1 text-sm"
                    onClick={() => onChange([...values, ''])}
                >
                    Add
                </button>
            </div>

            {values.map((value, index) => (
                <div key={index} className="flex gap-2">
                    <textarea
                        className="min-h-16 flex-1 rounded border p-2"
                        value={value}
                        onChange={(e) => update(index, e.target.value)}
                    />

                    <button
                        type="button"
                        className="rounded border px-3"
                        onClick={() =>
                            onChange(values.filter((_, i) => i !== index))
                        }
                    >
                        ×
                    </button>
                </div>
            ))}
        </div>
    );
}

export default function RequirementFields({ data, setData }: any) {
    return (
        <div className="space-y-6">
            <label className="block space-y-1">
                <span>Objective</span>

                <textarea
                    className="min-h-24 w-full rounded border p-2"
                    value={data.objective}
                    onChange={(e) => setData('objective', e.target.value)}
                />
            </label>

            <div className="grid gap-4 md:grid-cols-2">
                <label className="space-y-1">
                    <span>Scope Included</span>

                    <textarea
                        className="min-h-40 w-full rounded border p-2"
                        value={data.scope_in}
                        onChange={(e) => setData('scope_in', e.target.value)}
                    />
                </label>

                <label className="space-y-1">
                    <span>Scope Excluded</span>

                    <textarea
                        className="min-h-40 w-full rounded border p-2"
                        value={data.scope_out}
                        onChange={(e) => setData('scope_out', e.target.value)}
                    />
                </label>
            </div>

            <Lines
                title="Functional Requirements"
                values={data.functional_requirements}
                onChange={(value) => setData('functional_requirements', value)}
            />

            <Lines
                title="Non-Functional Requirements"
                values={data.non_functional_requirements}
                onChange={(value) =>
                    setData('non_functional_requirements', value)
                }
            />

            <Lines
                title="Assumptions"
                values={data.assumptions}
                onChange={(value) => setData('assumptions', value)}
            />

            <Lines
                title="Constraints"
                values={data.constraints}
                onChange={(value) => setData('constraints', value)}
            />

            <Lines
                title="Acceptance Criteria"
                values={data.acceptance_criteria}
                onChange={(value) => setData('acceptance_criteria', value)}
            />

            <label className="block space-y-1">
                <span>Client-visible Notes</span>

                <textarea
                    className="min-h-24 w-full rounded border p-2"
                    value={data.client_notes}
                    onChange={(e) => setData('client_notes', e.target.value)}
                />
            </label>

            <label className="block space-y-1">
                <span>Internal Notes</span>

                <textarea
                    className="min-h-24 w-full rounded border p-2"
                    value={data.internal_notes}
                    onChange={(e) => setData('internal_notes', e.target.value)}
                />
            </label>
        </div>
    );
}
