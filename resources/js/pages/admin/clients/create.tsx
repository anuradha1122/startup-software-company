import ClientForm from '@/components/m02/client-form';
import { Head, useForm } from '@inertiajs/react';

export default function CreateClient({ owners }: any) {
    const form = useForm({
        name: '',
        legal_name: '',
        registration_number: '',
        tax_number: '',
        email: '',
        phone: '',
        website: '',
        address: '',
        city: '',
        country: 'Sri Lanka',
        default_currency: 'LKR',
        default_payment_terms_days: '30',
        status: 'active',
        owner_id: '',
        notes: '',
    });

    return (
        <>
            <Head title="Create Client" />

            <div className="max-w-5xl p-6">
                <h1 className="mb-6 text-2xl font-semibold">Create Client</h1>

                <form
                    className="space-y-6"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post('/admin/clients');
                    }}
                >
                    <section className="rounded-lg border p-5">
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
                        Create Client
                    </button>
                </form>
            </div>
        </>
    );
}
