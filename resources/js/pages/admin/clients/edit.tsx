import ClientForm from '@/components/m02/client-form';
import { Head, useForm } from '@inertiajs/react';

export default function EditClient({ client, owners }: any) {
    const form = useForm({
        name: client.name ?? '',
        legal_name: client.legal_name ?? '',
        registration_number: client.registration_number ?? '',
        tax_number: client.tax_number ?? '',
        email: client.email ?? '',
        phone: client.phone ?? '',
        website: client.website ?? '',
        address: client.address ?? '',
        city: client.city ?? '',
        country: client.country ?? 'Sri Lanka',
        default_currency: client.default_currency ?? 'LKR',
        default_payment_terms_days: String(
            client.default_payment_terms_days ?? 30,
        ),
        status: client.status ?? 'active',
        owner_id: client.owner_id ? String(client.owner_id) : '',
        notes: client.notes ?? '',
    });

    return (
        <>
            <Head title={`Edit ${client.client_number}`} />

            <div className="max-w-5xl p-6">
                <h1 className="mb-6 text-2xl font-semibold">
                    Edit {client.client_number}
                </h1>

                <form
                    className="space-y-6"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.put(`/admin/clients/${client.public_id}`);
                    }}
                >
                    <section className="rounded border p-5">
                        <ClientForm
                            data={form.data}
                            setData={form.setData as any}
                            owners={owners}
                        />
                    </section>

                    <button
                        disabled={form.processing}
                        className="rounded bg-primary px-4 py-2 text-primary-foreground"
                    >
                        Save Changes
                    </button>
                </form>
            </div>
        </>
    );
}
