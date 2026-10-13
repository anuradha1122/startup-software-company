import { Head, Link, router } from '@inertiajs/react';

export default function PeopleIndex({ people, filters }: any) {
    return (
        <>
            <Head title="People" />

            <div className="space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">People</h1>

                    <p className="text-sm text-muted-foreground">
                        Operational resource profiles, skills and project
                        availability.
                    </p>
                </div>

                <form
                    className="flex gap-2"
                    onSubmit={(event) => {
                        event.preventDefault();

                        const data = new FormData(event.currentTarget);

                        router.get(
                            '/admin/people',
                            Object.fromEntries(data.entries()),
                            {
                                preserveState: true,
                            },
                        );
                    }}
                >
                    <input
                        name="search"
                        className="rounded border p-2"
                        placeholder="Search people"
                        defaultValue={filters.search ?? ''}
                    />

                    <button className="rounded border px-4 py-2">Search</button>
                </form>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Person</th>

                                <th className="p-3 text-left">Job Title</th>

                                <th className="p-3 text-left">Department</th>

                                <th className="p-3 text-left">Skills</th>

                                <th className="p-3 text-left">Available</th>
                            </tr>
                        </thead>

                        <tbody>
                            {people.data.map((person: any) => (
                                <tr key={person.public_id} className="border-t">
                                    <td className="p-3">
                                        <Link
                                            href={`/admin/people/${person.public_id}`}
                                            className="font-medium underline"
                                        >
                                            {person.name}
                                        </Link>

                                        <p className="text-xs text-muted-foreground">
                                            {person.email}
                                        </p>
                                    </td>

                                    <td className="p-3">
                                        {person.resource_profile?.job_title ??
                                            '-'}
                                    </td>

                                    <td className="p-3">
                                        {person.resource_profile?.department ??
                                            '-'}
                                    </td>

                                    <td className="p-3">
                                        {person.resource_profile?.skills
                                            ?.map((skill: any) => skill.name)
                                            .join(', ') || '-'}
                                    </td>

                                    <td className="p-3">
                                        {person.resource_profile
                                            ?.available_for_projects === false
                                            ? 'No'
                                            : 'Yes'}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}
