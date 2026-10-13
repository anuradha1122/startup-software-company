import { Head, useForm } from '@inertiajs/react';

type ProfileForm = {
    job_title: string;
    department: string;
    capacity_hours_per_week: number;
    default_allocation_percent: number;
    seniority: string;
    bio: string;
    available_for_projects: boolean;
};

export default function PersonShow({ person, skills }: any) {
    const profile = person.resource_profile;

    const form = useForm<ProfileForm>({
        job_title: profile?.job_title ?? '',

        department: profile?.department ?? '',

        capacity_hours_per_week: Number(profile?.capacity_hours_per_week ?? 40),

        default_allocation_percent: Number(
            profile?.default_allocation_percent ?? 100,
        ),

        seniority: profile?.seniority ?? '',

        bio: profile?.bio ?? '',

        available_for_projects: profile?.available_for_projects ?? true,
    });

    return (
        <>
            <Head title={person.name} />

            <div className="max-w-6xl space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">{person.name}</h1>

                    <p className="text-muted-foreground">{person.email}</p>
                </div>

                <form
                    className="space-y-4 rounded border p-5"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.put(`/admin/people/${person.public_id}`);
                    }}
                >
                    <div className="grid gap-4 md:grid-cols-2">
                        <input
                            className="rounded border p-2"
                            placeholder="Job title"
                            value={form.data.job_title}
                            onChange={(e) =>
                                form.setData('job_title', e.target.value)
                            }
                        />

                        <input
                            className="rounded border p-2"
                            placeholder="Department"
                            value={form.data.department}
                            onChange={(e) =>
                                form.setData('department', e.target.value)
                            }
                        />

                        <input
                            type="number"
                            min="1"
                            max="168"
                            className="rounded border p-2"
                            value={form.data.capacity_hours_per_week}
                            onChange={(e) =>
                                form.setData(
                                    'capacity_hours_per_week',
                                    Number(e.target.value),
                                )
                            }
                        />

                        <input
                            type="number"
                            min="1"
                            max="100"
                            className="rounded border p-2"
                            value={form.data.default_allocation_percent}
                            onChange={(e) =>
                                form.setData(
                                    'default_allocation_percent',
                                    Number(e.target.value),
                                )
                            }
                        />

                        <select
                            className="rounded border p-2"
                            value={form.data.seniority}
                            onChange={(e) =>
                                form.setData('seniority', e.target.value)
                            }
                        >
                            <option value="">Seniority</option>

                            {[
                                'trainee',
                                'junior',
                                'mid',
                                'senior',
                                'lead',
                                'principal',
                            ].map((value: string) => (
                                <option key={value} value={value}>
                                    {value}
                                </option>
                            ))}
                        </select>

                        <label className="flex items-center gap-2">
                            <input
                                type="checkbox"
                                checked={form.data.available_for_projects}
                                onChange={(e) =>
                                    form.setData(
                                        'available_for_projects',
                                        e.target.checked,
                                    )
                                }
                            />
                            Available for projects
                        </label>
                    </div>

                    <textarea
                        className="min-h-28 w-full rounded border p-2"
                        placeholder="Operational profile notes"
                        value={form.data.bio}
                        onChange={(e) => form.setData('bio', e.target.value)}
                    />

                    <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                        Save Profile
                    </button>
                </form>

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">Skills</h2>

                    {profile?.skills?.length ? (
                        <div className="space-y-2">
                            {profile.skills.map((skill: any) => (
                                <div
                                    key={skill.public_id}
                                    className="rounded border p-3"
                                >
                                    <strong>{skill.name}</strong>

                                    <p className="text-sm text-muted-foreground">
                                        Proficiency: {skill.pivot.proficiency}
                                        /5
                                    </p>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-sm text-muted-foreground">
                            No skills recorded.
                        </p>
                    )}

                    <p className="mt-4 text-sm text-muted-foreground">
                        Available skill catalogue:{' '}
                        {skills.map((skill: any) => skill.name).join(', ')}
                    </p>
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Project Assignments
                    </h2>

                    {person.project_assignments.map((assignment: any) => (
                        <div
                            key={assignment.public_id}
                            className="mb-2 rounded border p-3"
                        >
                            <strong>{assignment.project.project_number}</strong>

                            {' — '}

                            {assignment.role}

                            <p className="text-sm text-muted-foreground">
                                {assignment.allocation_percent}%{' · '}
                                {assignment.status}
                            </p>
                        </div>
                    ))}
                </section>
            </div>
        </>
    );
}
