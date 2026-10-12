import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';

type Role = {
    id: number;
    name: string;
    permissions: Array<{
        id: number;
        name: string;
    }>;
};

type Props = {
    roles: Role[];
    permissions: string[];
    coreRoles: string[];
};

function RoleCard({
    role,
    permissions,
}: {
    role: Role;
    permissions: string[];
}) {
    const [selected, setSelected] = useState(
        role.permissions.map((item) => item.name),
    );

    const locked = ['Director', 'Admin'].includes(role.name);

    const toggle = (permission: string) => {
        if (locked) {
            return;
        }

        setSelected((current) =>
            current.includes(permission)
                ? current.filter((item) => item !== permission)
                : [...current, permission],
        );
    };

    return (
        <div className="rounded border p-5">
            <h2 className="mb-4 text-lg font-semibold">{role.name}</h2>

            {locked && (
                <p className="mb-4 text-sm text-muted-foreground">
                    This core role always has all permissions.
                </p>
            )}

            <div className="grid gap-2 md:grid-cols-2">
                {permissions.map((permission) => (
                    <label key={permission} className="flex items-center gap-2">
                        <input
                            type="checkbox"
                            checked={locked || selected.includes(permission)}
                            disabled={locked}
                            onChange={() => toggle(permission)}
                        />

                        {permission}
                    </label>
                ))}
            </div>

            <button
                className="mt-4 rounded bg-slate-900 px-4 py-2 text-white"
                onClick={() =>
                    router.put(`/admin/roles/${role.id}`, {
                        permissions: selected,
                    })
                }
            >
                Save permissions
            </button>
        </div>
    );
}

export default function RolesIndex({ roles, permissions }: Props) {
    const createForm = useForm({
        name: '',
    });

    return (
        <>
            <Head title="Roles & Permissions" />

            <div className="p-6">
                <h1 className="mb-6 text-2xl font-semibold">
                    Roles & Permissions
                </h1>

                <form
                    className="mb-8 flex max-w-xl gap-2"
                    onSubmit={(event) => {
                        event.preventDefault();

                        createForm.post('/admin/roles', {
                            onSuccess: () => createForm.reset(),
                        });
                    }}
                >
                    <input
                        className="flex-1 rounded border p-2"
                        placeholder="Custom role name"
                        value={createForm.data.name}
                        onChange={(e) =>
                            createForm.setData('name', e.target.value)
                        }
                    />

                    <button className="rounded bg-slate-900 px-4 py-2 text-white">
                        Add role
                    </button>
                </form>

                <div className="grid gap-6 lg:grid-cols-2">
                    {roles.map((role) => (
                        <RoleCard
                            key={role.id}
                            role={role}
                            permissions={permissions}
                        />
                    ))}
                </div>
            </div>
        </>
    );
}
