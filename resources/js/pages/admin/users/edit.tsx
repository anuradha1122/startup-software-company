import { Head, useForm } from "@inertiajs/react";

type ManagedUser = {
    public_id: string;
    name: string;
    email: string;
    phone?: string | null;
    status: string;
    roles: string[];
};

type Props = {
    managedUser: ManagedUser;
    roles: string[];
};

export default function EditUser({ managedUser, roles }: Props) {
    const form = useForm({
        name: managedUser.name,
        email: managedUser.email,
        phone: managedUser.phone ?? "",
        status: managedUser.status,
        roles: managedUser.roles,
    });

    const toggleRole = (role: string) => {
        form.setData(
            "roles",
            form.data.roles.includes(role)
                ? form.data.roles.filter((item) => item !== role)
                : [...form.data.roles, role],
        );
    };

    return (
        <>
            <Head title={`Edit ${managedUser.name}`} />

            <div className="max-w-4xl p-6">
                <h1 className="mb-6 text-2xl font-semibold">Edit user</h1>

                <form
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.put(`/admin/users/${managedUser.public_id}`);
                    }}
                    className="space-y-4 rounded border p-6"
                >
                    <input
                        className="w-full rounded border p-2"
                        value={form.data.name}
                        onChange={(e) => form.setData("name", e.target.value)}
                    />

                    <input
                        type="email"
                        className="w-full rounded border p-2"
                        value={form.data.email}
                        onChange={(e) => form.setData("email", e.target.value)}
                    />

                    <input
                        className="w-full rounded border p-2"
                        value={form.data.phone}
                        onChange={(e) => form.setData("phone", e.target.value)}
                    />

                    <select
                        className="w-full rounded bg-white dark:bg-gray-950 border p-2"
                        value={form.data.status}
                        onChange={(e) => form.setData("status", e.target.value)}
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>

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

                    <button
                        className="rounded bg-slate-900 px-4 py-2 text-white"
                        disabled={form.processing}
                    >
                        Save changes
                    </button>
                </form>
            </div>
        </>
    );
}
