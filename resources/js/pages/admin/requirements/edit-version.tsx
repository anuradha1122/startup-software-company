import RequirementFields from '@/components/m03/requirement-fields';
import { Head, useForm } from '@inertiajs/react';

const arrayOrBlank = (value?: string[] | null) => {
    return value?.length ? value : [''];
};

export default function EditVersion({ requirement, version }: any) {
    const form = useForm({
        version_title: version.version_title ?? '',

        objective: version.objective ?? '',

        scope_in: version.scope_in ?? '',

        scope_out: version.scope_out ?? '',

        functional_requirements: arrayOrBlank(version.functional_requirements),

        non_functional_requirements: arrayOrBlank(
            version.non_functional_requirements,
        ),

        assumptions: arrayOrBlank(version.assumptions),

        constraints: arrayOrBlank(version.constraints),

        acceptance_criteria: arrayOrBlank(version.acceptance_criteria),

        client_notes: version.client_notes ?? '',

        internal_notes: version.internal_notes ?? '',
    });

    return (
        <>
            <Head
                title={`${requirement.requirement_number} v${version.version_number}`}
            />

            <div className="max-w-6xl p-6">
                <h1 className="mb-2 text-2xl font-semibold">
                    {requirement.requirement_number}
                    {' — '}
                    Version {version.version_number}
                </h1>

                <p className="mb-6 text-muted-foreground">
                    {requirement.title}
                </p>

                <form
                    className="space-y-6"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.put(
                            `/admin/requirements/${requirement.public_id}/versions/${version.public_id}`,
                        );
                    }}
                >
                    <label className="block space-y-1">
                        <span>Version Title</span>

                        <input
                            className="w-full rounded border p-2"
                            value={form.data.version_title}
                            onChange={(e) =>
                                form.setData('version_title', e.target.value)
                            }
                        />
                    </label>

                    <RequirementFields
                        data={form.data}
                        setData={form.setData as any}
                    />

                    <button
                        disabled={form.processing}
                        className="rounded bg-primary px-4 py-2 text-primary-foreground"
                    >
                        Save Draft
                    </button>
                </form>
            </div>
        </>
    );
}
