import { Head, Link, router, useForm } from '@inertiajs/react';

export default function ClientShow({ client }: any) {
    const contact = useForm({
        name: '',
        designation: '',
        email: '',
        phone: '',
        is_primary: false,
        is_billing_contact: false,
        is_decision_maker: false,
        status: 'active',
        notes: '',
    });

    const billing = useForm({
        billing_name: client.name ?? '',
        billing_email: client.email ?? '',
        billing_phone: client.phone ?? '',
        billing_address: client.address ?? '',
        city: client.city ?? '',
        country: client.country ?? 'Sri Lanka',
        tax_number: client.tax_number ?? '',
        currency: client.default_currency ?? 'LKR',
        payment_terms_days: client.default_payment_terms_days ?? 30,
        invoice_notes: '',
        is_default: client.billing_profiles?.length === 0,
    });

    const documentForm = useForm({
        title: '',
        category: 'other',
        visibility: 'internal',
        file: null as File | null,
    });

    return (
        <>
            <Head title={client.client_number} />

            <div className="max-w-7xl p-6">
                <div className="mb-6 flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {client.client_number}
                        </h1>

                        <p className="text-lg">{client.name}</p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href={`/admin/clients/${client.public_id}/profile-pdf`}
                            className="rounded border px-3 py-2"
                        >
                            Profile PDF
                        </a>

                        <Link
                            href={`/admin/clients/${client.public_id}/edit`}
                            className="rounded border px-3 py-2"
                        >
                            Edit
                        </Link>
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    <section className="rounded border p-5 lg:col-span-2">
                        <h2 className="mb-4 text-lg font-semibold">
                            Client Profile
                        </h2>

                        <dl className="grid gap-4 md:grid-cols-2">
                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Legal Name
                                </dt>
                                <dd>{client.legal_name ?? '-'}</dd>
                            </div>

                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Owner
                                </dt>
                                <dd>{client.owner?.name ?? 'Unassigned'}</dd>
                            </div>

                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Email
                                </dt>
                                <dd>{client.email ?? '-'}</dd>
                            </div>

                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Phone
                                </dt>
                                <dd>{client.phone ?? '-'}</dd>
                            </div>

                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Currency
                                </dt>
                                <dd>{client.default_currency}</dd>
                            </div>

                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Payment Terms
                                </dt>
                                <dd>
                                    {client.default_payment_terms_days} days
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <section className="rounded border p-5">
                        <h2 className="mb-4 text-lg font-semibold">Address</h2>

                        <p>{client.address}</p>

                        <p>{client.city}</p>

                        <p>{client.country}</p>
                    </section>
                </div>

                <section className="mt-6 rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">Contacts</h2>

                    <form
                        className="mb-6 grid gap-3 md:grid-cols-2"
                        onSubmit={(event) => {
                            event.preventDefault();

                            contact.post(
                                `/admin/clients/${client.public_id}/contacts`,
                                {
                                    preserveScroll: true,

                                    onSuccess: () => contact.reset(),
                                },
                            );
                        }}
                    >
                        <input
                            placeholder="Name"
                            className="rounded border p-2"
                            value={contact.data.name}
                            onChange={(e) =>
                                contact.setData('name', e.target.value)
                            }
                        />

                        <input
                            placeholder="Designation"
                            className="rounded border p-2"
                            value={contact.data.designation}
                            onChange={(e) =>
                                contact.setData('designation', e.target.value)
                            }
                        />

                        <input
                            type="email"
                            placeholder="Email"
                            className="rounded border p-2"
                            value={contact.data.email}
                            onChange={(e) =>
                                contact.setData('email', e.target.value)
                            }
                        />

                        <input
                            placeholder="Phone"
                            className="rounded border p-2"
                            value={contact.data.phone}
                            onChange={(e) =>
                                contact.setData('phone', e.target.value)
                            }
                        />

                        <div className="flex flex-wrap gap-4 md:col-span-2">
                            <label>
                                <input
                                    type="checkbox"
                                    checked={contact.data.is_primary}
                                    onChange={(e) =>
                                        contact.setData(
                                            'is_primary',
                                            e.target.checked,
                                        )
                                    }
                                />{' '}
                                Primary
                            </label>

                            <label>
                                <input
                                    type="checkbox"
                                    checked={contact.data.is_billing_contact}
                                    onChange={(e) =>
                                        contact.setData(
                                            'is_billing_contact',
                                            e.target.checked,
                                        )
                                    }
                                />{' '}
                                Billing Contact
                            </label>

                            <label>
                                <input
                                    type="checkbox"
                                    checked={contact.data.is_decision_maker}
                                    onChange={(e) =>
                                        contact.setData(
                                            'is_decision_maker',
                                            e.target.checked,
                                        )
                                    }
                                />{' '}
                                Decision Maker
                            </label>
                        </div>

                        <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                            Add Contact
                        </button>
                    </form>

                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b">
                                    <th className="p-2 text-left">Name</th>

                                    <th className="p-2 text-left">Email</th>

                                    <th className="p-2 text-left">Phone</th>

                                    <th className="p-2 text-left">Type</th>

                                    <th />
                                </tr>
                            </thead>

                            <tbody>
                                {client.contacts.map((item: any) => (
                                    <tr
                                        key={item.public_id}
                                        className="border-b"
                                    >
                                        <td className="p-2">{item.name}</td>

                                        <td className="p-2">{item.email}</td>

                                        <td className="p-2">{item.phone}</td>

                                        <td className="p-2">
                                            {item.is_primary && 'Primary '}

                                            {item.is_billing_contact &&
                                                'Billing '}
                                        </td>

                                        <td className="p-2 text-right">
                                            <button
                                                className="text-red-600 underline"
                                                onClick={() => {
                                                    if (
                                                        confirm(
                                                            'Archive this contact?',
                                                        )
                                                    ) {
                                                        router.delete(
                                                            `/admin/clients/${client.public_id}/contacts/${item.public_id}`,
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
                </section>

                <section className="mt-6 rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Billing Profiles
                    </h2>

                    <form
                        className="mb-6 grid gap-3 md:grid-cols-2"
                        onSubmit={(event) => {
                            event.preventDefault();

                            billing.post(
                                `/admin/clients/${client.public_id}/billing-profiles`,
                                {
                                    preserveScroll: true,
                                },
                            );
                        }}
                    >
                        <input
                            placeholder="Billing Name"
                            className="rounded border p-2"
                            value={billing.data.billing_name}
                            onChange={(e) =>
                                billing.setData('billing_name', e.target.value)
                            }
                        />

                        <input
                            type="email"
                            placeholder="Billing Email"
                            className="rounded border p-2"
                            value={billing.data.billing_email}
                            onChange={(e) =>
                                billing.setData('billing_email', e.target.value)
                            }
                        />

                        <input
                            placeholder="Tax Number"
                            className="rounded border p-2"
                            value={billing.data.tax_number}
                            onChange={(e) =>
                                billing.setData('tax_number', e.target.value)
                            }
                        />

                        <input
                            type="number"
                            placeholder="Payment Terms"
                            className="rounded border p-2"
                            value={billing.data.payment_terms_days}
                            onChange={(e) =>
                                billing.setData(
                                    'payment_terms_days',
                                    Number(e.target.value),
                                )
                            }
                        />

                        <textarea
                            placeholder="Billing Address"
                            className="rounded border p-2 md:col-span-2"
                            value={billing.data.billing_address}
                            onChange={(e) =>
                                billing.setData(
                                    'billing_address',
                                    e.target.value,
                                )
                            }
                        />

                        <label>
                            <input
                                type="checkbox"
                                checked={billing.data.is_default}
                                onChange={(e) =>
                                    billing.setData(
                                        'is_default',
                                        e.target.checked,
                                    )
                                }
                            />{' '}
                            Default billing profile
                        </label>

                        <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                            Add Billing Profile
                        </button>
                    </form>

                    {client.billing_profiles.map((profile: any) => (
                        <div
                            key={profile.public_id}
                            className="mb-3 rounded border p-4"
                        >
                            <p className="font-medium">
                                {profile.billing_name}

                                {profile.is_default && ' — Default'}
                            </p>

                            <p className="text-sm">
                                {profile.currency}
                                {' · '}
                                {profile.payment_terms_days} days
                            </p>
                        </div>
                    ))}
                </section>

                <section className="mt-6 rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Client Documents
                    </h2>

                    <form
                        className="mb-6 grid gap-3 md:grid-cols-2"
                        onSubmit={(event) => {
                            event.preventDefault();

                            documentForm.post(
                                `/admin/clients/${client.public_id}/documents`,
                                {
                                    forceFormData: true,
                                    preserveScroll: true,
                                },
                            );
                        }}
                    >
                        <input
                            placeholder="Document Title"
                            className="rounded border p-2"
                            value={documentForm.data.title}
                            onChange={(e) =>
                                documentForm.setData('title', e.target.value)
                            }
                        />

                        <select
                            className="rounded border p-2"
                            value={documentForm.data.category}
                            onChange={(e) =>
                                documentForm.setData('category', e.target.value)
                            }
                        >
                            <option value="registration">Registration</option>

                            <option value="tax">Tax</option>

                            <option value="agreement">Agreement</option>

                            <option value="billing">Billing</option>

                            <option value="identification">
                                Identification
                            </option>

                            <option value="other">Other</option>
                        </select>

                        <select
                            className="rounded border p-2"
                            value={documentForm.data.visibility}
                            onChange={(e) =>
                                documentForm.setData(
                                    'visibility',
                                    e.target.value,
                                )
                            }
                        >
                            <option value="internal">Internal</option>

                            <option value="client">Client-visible</option>
                        </select>

                        <input
                            type="file"
                            onChange={(e) =>
                                documentForm.setData(
                                    'file',
                                    e.target.files?.[0] ?? null,
                                )
                            }
                        />

                        <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                            Upload Document
                        </button>
                    </form>

                    <div className="space-y-3">
                        {client.documents.map((document: any) => (
                            <div
                                key={document.public_id}
                                className="flex flex-wrap items-center justify-between gap-3 rounded border p-4"
                            >
                                <div>
                                    <p className="font-medium">
                                        {document.title}
                                    </p>

                                    <p className="text-sm text-muted-foreground">
                                        {document.category}
                                        {' · '}
                                        {document.original_name}
                                    </p>
                                </div>

                                <div className="flex gap-3">
                                    <a
                                        className="underline"
                                        href={`/admin/clients/${client.public_id}/documents/${document.public_id}/download`}
                                    >
                                        Download
                                    </a>

                                    <button
                                        className="text-red-600 underline"
                                        onClick={() => {
                                            if (
                                                confirm('Delete this document?')
                                            ) {
                                                router.delete(
                                                    `/admin/clients/${client.public_id}/documents/${document.public_id}`,
                                                );
                                            }
                                        }}
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                </section>
            </div>
        </>
    );
}
