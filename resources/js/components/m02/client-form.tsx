type Owner = {
    id: number;
    name: string;
};

type ClientFormData = {
    name: string;
    legal_name: string;
    registration_number: string;
    tax_number: string;
    email: string;
    phone: string;
    website: string;
    address: string;
    city: string;
    country: string;
    default_currency: string;
    default_payment_terms_days: string;
    status: string;
    owner_id: string;
    notes: string;
};

export default function ClientForm({
    data,
    setData,
    owners,
}: {
    data: ClientFormData;
    setData: (key: keyof ClientFormData, value: string) => void;
    owners: Owner[];
}) {
    return (
        <div className="grid gap-4 md:grid-cols-2">
            <label className="space-y-1">
                <span>Client Name *</span>

                <input
                    className="w-full rounded border p-2"
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                />
            </label>

            <label className="space-y-1">
                <span>Legal Name</span>

                <input
                    className="w-full rounded border p-2"
                    value={data.legal_name}
                    onChange={(e) => setData('legal_name', e.target.value)}
                />
            </label>

            <label className="space-y-1">
                <span>Registration Number</span>

                <input
                    className="w-full rounded border p-2"
                    value={data.registration_number}
                    onChange={(e) =>
                        setData('registration_number', e.target.value)
                    }
                />
            </label>

            <label className="space-y-1">
                <span>Tax Number</span>

                <input
                    className="w-full rounded border p-2"
                    value={data.tax_number}
                    onChange={(e) => setData('tax_number', e.target.value)}
                />
            </label>

            <label className="space-y-1">
                <span>Email</span>

                <input
                    type="email"
                    className="w-full rounded border p-2"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                />
            </label>

            <label className="space-y-1">
                <span>Phone</span>

                <input
                    className="w-full rounded border p-2"
                    value={data.phone}
                    onChange={(e) => setData('phone', e.target.value)}
                />
            </label>

            <label className="space-y-1">
                <span>Website</span>

                <input
                    className="w-full rounded border p-2"
                    value={data.website}
                    onChange={(e) => setData('website', e.target.value)}
                />
            </label>

            <label className="space-y-1">
                <span>Owner</span>

                <select
                    className="w-full rounded border p-2"
                    value={data.owner_id}
                    onChange={(e) => setData('owner_id', e.target.value)}
                >
                    <option value="">Unassigned</option>

                    {owners.map((owner) => (
                        <option key={owner.id} value={owner.id}>
                            {owner.name}
                        </option>
                    ))}
                </select>
            </label>

            <label className="space-y-1">
                <span>Country</span>

                <input
                    className="w-full rounded border p-2"
                    value={data.country}
                    onChange={(e) => setData('country', e.target.value)}
                />
            </label>

            <label className="space-y-1">
                <span>City</span>

                <input
                    className="w-full rounded border p-2"
                    value={data.city}
                    onChange={(e) => setData('city', e.target.value)}
                />
            </label>

            <label className="space-y-1">
                <span>Currency</span>

                <input
                    maxLength={3}
                    className="w-full rounded border p-2"
                    value={data.default_currency}
                    onChange={(e) =>
                        setData(
                            'default_currency',
                            e.target.value.toUpperCase(),
                        )
                    }
                />
            </label>

            <label className="space-y-1">
                <span>Payment Terms (Days)</span>

                <input
                    type="number"
                    min="0"
                    max="365"
                    className="w-full rounded border p-2"
                    value={data.default_payment_terms_days}
                    onChange={(e) =>
                        setData('default_payment_terms_days', e.target.value)
                    }
                />
            </label>

            <label className="space-y-1">
                <span>Status</span>

                <select
                    className="w-full rounded border p-2"
                    value={data.status}
                    onChange={(e) => setData('status', e.target.value)}
                >
                    <option value="active">Active</option>

                    <option value="inactive">Inactive</option>
                </select>
            </label>

            <label className="space-y-1 md:col-span-2">
                <span>Address</span>

                <textarea
                    className="min-h-24 w-full rounded border p-2"
                    value={data.address}
                    onChange={(e) => setData('address', e.target.value)}
                />
            </label>

            <label className="space-y-1 md:col-span-2">
                <span>Internal Notes</span>

                <textarea
                    className="min-h-28 w-full rounded border p-2"
                    value={data.notes}
                    onChange={(e) => setData('notes', e.target.value)}
                />
            </label>
        </div>
    );
}
