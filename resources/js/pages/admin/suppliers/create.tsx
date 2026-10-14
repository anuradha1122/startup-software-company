import { Head, useForm } from '@inertiajs/react';

type SupplierForm = {
    name: string;
    legal_name: string;
    email: string;
    phone: string;
    website: string;
    tax_number: string;
    address: string;
    city: string;
    country: string;
    default_currency: string;
    notes: string;
};

export default function SupplierCreate() {
    const form = useForm<SupplierForm>({
        name: '',
        legal_name: '',
        email: '',
        phone: '',
        website: '',
        tax_number: '',
        address: '',
        city: '',
        country: 'Sri Lanka',
        default_currency: 'LKR',
        notes: '',
    });

    return (
        <>
            <Head title="New Supplier" />

            <form
                className="mx-auto max-w-5xl space-y-4 p-6"
                onSubmit={(event) => {
                    event.preventDefault();

                    form.post('/admin/suppliers');
                }}
            >
                <h1 className="text-2xl font-semibold">New Supplier</h1>

                <div className="grid gap-4 md:grid-cols-2">
                    <input
                        className="rounded border p-2"
                        placeholder="Supplier name"
                        value={form.data.name}
                        onChange={(e) => form.setData('name', e.target.value)}
                    />

                    <input
                        className="rounded border p-2"
                        placeholder="Legal name"
                        value={form.data.legal_name}
                        onChange={(e) =>
                            form.setData('legal_name', e.target.value)
                        }
                    />

                    <input
                        className="rounded border p-2"
                        placeholder="Email"
                        value={form.data.email}
                        onChange={(e) => form.setData('email', e.target.value)}
                    />

                    <input
                        className="rounded border p-2"
                        placeholder="Phone"
                        value={form.data.phone}
                        onChange={(e) => form.setData('phone', e.target.value)}
                    />

                    <input
                        className="rounded border p-2"
                        placeholder="Currency"
                        maxLength={3}
                        value={form.data.default_currency}
                        onChange={(e) =>
                            form.setData(
                                'default_currency',
                                e.target.value.toUpperCase(),
                            )
                        }
                    />

                    <input
                        className="rounded border p-2"
                        placeholder="Country"
                        value={form.data.country}
                        onChange={(e) =>
                            form.setData('country', e.target.value)
                        }
                    />
                </div>

                <textarea
                    className="min-h-24 w-full rounded border p-2"
                    placeholder="Address"
                    value={form.data.address}
                    onChange={(e) => form.setData('address', e.target.value)}
                />

                <textarea
                    className="min-h-24 w-full rounded border p-2"
                    placeholder="Notes"
                    value={form.data.notes}
                    onChange={(e) => form.setData('notes', e.target.value)}
                />

                <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                    Create Supplier
                </button>
            </form>
        </>
    );
}
