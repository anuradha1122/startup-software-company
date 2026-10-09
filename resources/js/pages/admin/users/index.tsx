import { Head, Link, router, useForm } from "@inertiajs/react";

type Role = {
    id: number;
    name: string;
};

type User = {
    public_id: string;
    name: string;
    email: string;
    phone?: string | null;
    status: string;
    roles: Role[];
};

type Props = {
    users: {
        data: User[];
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    roles: string[];
};

export default function UsersIndex({ users, roles }: Props) {
    const form = useForm({
        name: "",
        email: "",
        phone: "",
        status: "active",
        roles: [] as string[],
    });

    const toggleRole = (role: string) => {
        form.setData(
            "roles",
            form.data.roles.includes(role)
                ? form.data.roles.filter((item) => item !== role)
                : [...form.data.roles, role],
        );
    };

    const submit = (event: React.FormEvent) => {
        event.preventDefault();

        form.post("/admin/users", {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };

    return (
        <>
            <Head title="Users" />

            <div className="p-6">
                <div className="mb-6 flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold">Users</h1>
                        <p className="text-sm text-muted-foreground">
                            Manage IT Hub accounts and access.
                        </p>
                    </div>

                    <a
                        href="/admin/users/export"
                        className="rounded bg-slate-900 px-4 py-2 text-sm text-white"
                    >
                        Export Excel
                    </a>
                </div>

                <form onSubmit={submit} className="mb-8 rounded-lg border p-5">
                    <h2 className="mb-4 text-lg font-medium">Create user</h2>

                    <div className="grid gap-4 md:grid-cols-2">
                        <input
                            className="rounded border p-2"
                            placeholder="Full name"
                            value={form.data.name}
                            onChange={(e) =>
                                form.setData("name", e.target.value)
                            }
                        />

                        <input
                            className="rounded border p-2"
                            type="email"
                            placeholder="Email"
                            value={form.data.email}
                            onChange={(e) =>
                                form.setData("email", e.target.value)
                            }
                        />

                        <input
                            className="rounded border p-2"
                            placeholder="Phone"
                            value={form.data.phone}
                            onChange={(e) =>
                                form.setData("phone", e.target.value)
                            }
                        />

                        <select
                            className="rounded border bg-white dark:bg-gray-950 p-2"
                            value={form.data.status}
                            onChange={(e) =>
                                form.setData("status", e.target.value)
                            }
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>

                    <div className="mt-4">
                        <p className="mb-2 text-sm font-medium">Roles</p>

                        <div className="flex flex-wrap gap-4">
                            {roles.map((role) => (
                                <label
                                    key={role}
                                    className="flex items-center gap-2"
                                >
                                    <input
                                        type="checkbox"
                                        checked={form.data.roles.includes(role)}
                                        onChange={() => toggleRole(role)}
                                    />
                                    {role}
                                </label>
                            ))}
                        </div>
                    </div>

                    <button
                        disabled={form.processing}
                        className="mt-5 rounded bg-slate-900 px-4 py-2 text-white"
                    >
                        Create user
                    </button>
                </form>

                <div className="overflow-x-auto rounded-lg border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Name</th>
                                <th className="p-3 text-left">Email</th>
                                <th className="p-3 text-left">Roles</th>
                                <th className="p-3 text-left">Status</th>
                                <th className="p-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            {users.data.map((user) => (
                                <tr key={user.public_id} className="border-t">
                                    <td className="p-3">{user.name}</td>

                                    <td className="p-3">{user.email}</td>

                                    <td className="p-3">
                                        {user.roles
                                            .map((role) => role.name)
                                            .join(", ")}
                                    </td>

                                    <td className="p-3">{user.status}</td>

                                    <td className="p-3 text-right">
                                        <Link
                                            href={`/admin/users/${user.public_id}/edit`}
                                            className="mr-3 underline"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            className="text-red-600 underline"
                                            onClick={() => {
                                                if (
                                                    confirm(
                                                        "Archive this user?",
                                                    )
                                                ) {
                                                    router.delete(
                                                        `/admin/users/${user.public_id}`,
                                                    );
                                                }
                                            }}
                                        >
                                            Archive
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                <div className="mt-4 flex flex-wrap gap-2">
                    {users.links.map((link, index) => (
                        <button
                            key={index}
                            disabled={!link.url}
                            onClick={() => link.url && router.get(link.url)}
                            className={`rounded border px-3 py-1 ${
                                link.active ? "bg-slate-900 text-white" : ""
                            }`}
                            dangerouslySetInnerHTML={{
                                __html: link.label,
                            }}
                        />
                    ))}
                </div>
            </div>
        </>
    );
}
