import { Head, useForm } from '@inertiajs/react';

type ProjectForm = {
    contract_id: string;
    name: string;
    methodology: string;
    description: string;
    project_manager_id: string;
    client_sponsor_contact_id: string;
    planned_start_date: string;
    planned_end_date: string;
};

export default function CreateProject({ contracts, projectManagers }: any) {
    const form = useForm<ProjectForm>({
        contract_id: '',
        name: '',
        methodology: 'Agile',
        description: '',
        project_manager_id: '',
        client_sponsor_contact_id: '',
        planned_start_date: '',
        planned_end_date: '',
    });

    const contract = contracts.find(
        (item: any) => String(item.id) === form.data.contract_id,
    );

    return (
        <>
            <Head title="Create Project" />

            <div className="max-w-5xl p-6">
                <h1 className="mb-6 text-2xl font-semibold">Create Project</h1>

                <form
                    className="space-y-5"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post('/admin/projects');
                    }}
                >
                    <label className="block space-y-1">
                        <span>Signed Contract</span>

                        <select
                            className="w-full rounded border p-2"
                            value={form.data.contract_id}
                            onChange={(e) => {
                                form.setData('contract_id', e.target.value);

                                form.setData('client_sponsor_contact_id', '');
                            }}
                        >
                            <option value="">Select signed contract</option>

                            {contracts.map((item: any) => (
                                <option key={item.id} value={item.id}>
                                    {item.contract_number}
                                    {' — '}
                                    {item.client.name}
                                </option>
                            ))}
                        </select>
                    </label>

                    {contract && (
                        <div className="grid gap-4 rounded border p-4 md:grid-cols-3">
                            <div>
                                <span className="text-sm text-muted-foreground">
                                    Client
                                </span>

                                <p>{contract.client.name}</p>
                            </div>

                            <div>
                                <span className="text-sm text-muted-foreground">
                                    Contract Value
                                </span>

                                <p>
                                    {contract.currency}{' '}
                                    {contract.contract_value}
                                </p>
                            </div>

                            <div>
                                <span className="text-sm text-muted-foreground">
                                    Status
                                </span>

                                <p>{contract.status}</p>
                            </div>
                        </div>
                    )}

                    <input
                        className="w-full rounded border p-2"
                        placeholder="Project name"
                        value={form.data.name}
                        onChange={(e) => form.setData('name', e.target.value)}
                    />

                    <div className="grid gap-4 md:grid-cols-2">
                        <select
                            className="rounded border p-2"
                            value={form.data.project_manager_id}
                            onChange={(e) =>
                                form.setData(
                                    'project_manager_id',
                                    e.target.value,
                                )
                            }
                        >
                            <option value="">Project Manager</option>

                            {projectManagers.map((user: any) => (
                                <option key={user.id} value={user.id}>
                                    {user.name}
                                </option>
                            ))}
                        </select>

                        <select
                            className="rounded border p-2"
                            value={form.data.client_sponsor_contact_id}
                            onChange={(e) =>
                                form.setData(
                                    'client_sponsor_contact_id',
                                    e.target.value,
                                )
                            }
                        >
                            <option value="">Client Sponsor</option>

                            {contract?.client?.contacts?.map((contact: any) => (
                                <option key={contact.id} value={contact.id}>
                                    {contact.name}
                                </option>
                            ))}
                        </select>
                    </div>

                    <input
                        className="w-full rounded border p-2"
                        placeholder="Methodology"
                        value={form.data.methodology}
                        onChange={(e) =>
                            form.setData('methodology', e.target.value)
                        }
                    />

                    <textarea
                        className="min-h-28 w-full rounded border p-2"
                        placeholder="Project description"
                        value={form.data.description}
                        onChange={(e) =>
                            form.setData('description', e.target.value)
                        }
                    />

                    <div className="grid gap-4 md:grid-cols-2">
                        <input
                            type="date"
                            className="rounded border p-2"
                            value={form.data.planned_start_date}
                            onChange={(e) =>
                                form.setData(
                                    'planned_start_date',
                                    e.target.value,
                                )
                            }
                        />

                        <input
                            type="date"
                            className="rounded border p-2"
                            value={form.data.planned_end_date}
                            onChange={(e) =>
                                form.setData('planned_end_date', e.target.value)
                            }
                        />
                    </div>

                    <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                        Create Project
                    </button>
                </form>
            </div>
        </>
    );
}
