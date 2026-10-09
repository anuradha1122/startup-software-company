import { Head, useForm } from "@inertiajs/react";

export default function CompanyEdit({ setting }: { setting: any }) {
    const form = useForm({
        _method: "put",

        legal_name: setting.legal_name ?? "",
        trading_name: setting.trading_name ?? "",
        registration_number: setting.registration_number ?? "",
        tax_number: setting.tax_number ?? "",
        email: setting.email ?? "",
        phone: setting.phone ?? "",
        website: setting.website ?? "",
        address: setting.address ?? "",
        country: setting.country ?? "Sri Lanka",
        currency: setting.currency ?? "LKR",
        timezone: setting.timezone ?? "Asia/Colombo",

        bank_name: setting.bank_name ?? "",
        bank_branch: setting.bank_branch ?? "",
        bank_account_name: setting.bank_account_name ?? "",
        bank_account_number: setting.bank_account_number ?? "",
        bank_swift_code: setting.bank_swift_code ?? "",

        document_footer: setting.document_footer ?? "",

        logo: null as File | null,
    });

    const field = (
        key: keyof typeof form.data,
        label: string,
        type = "text",
    ) => (
        <label className="space-y-1">
            <span className="text-sm font-medium">{label}</span>

            <input
                type={type}
                className="w-full rounded border p-2"
                value={
                    typeof form.data[key] === "string"
                        ? (form.data[key] as string)
                        : ""
                }
                onChange={(e) => form.setData(key, e.target.value as never)}
            />
        </label>
    );

    return (
        <>
            <Head title="Company Settings" />

            <div className="max-w-5xl p-6">
                <h1 className="mb-6 text-2xl font-semibold">
                    Company Settings
                </h1>

                <form
                    className="space-y-8"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post("/admin/company", {
                            forceFormData: true,
                        });
                    }}
                >
                    <section className="grid gap-4 rounded border p-5 md:grid-cols-2">
                        {field("legal_name", "Legal name")}

                        {field("trading_name", "Trading name")}

                        {field("registration_number", "Registration number")}

                        {field("tax_number", "Tax number")}

                        {field("email", "Email", "email")}
                        {field("phone", "Phone")}
                        {field("website", "Website")}
                        {field("country", "Country")}
                        {field("currency", "Currency")}
                        {field("timezone", "Timezone")}

                        <label className="space-y-1 md:col-span-2">
                            <span className="text-sm font-medium">Address</span>

                            <textarea
                                className="min-h-24 w-full rounded border p-2"
                                value={form.data.address}
                                onChange={(e) =>
                                    form.setData("address", e.target.value)
                                }
                            />
                        </label>
                    </section>

                    <section className="grid gap-4 rounded border p-5 md:grid-cols-2">
                        <h2 className="md:col-span-2 text-lg font-semibold">
                            Bank details
                        </h2>

                        {field("bank_name", "Bank name")}

                        {field("bank_branch", "Branch")}

                        {field("bank_account_name", "Account name")}

                        {field("bank_account_number", "Account number")}

                        {field("bank_swift_code", "SWIFT code")}
                    </section>

                    <section className="space-y-4 rounded border p-5">
                        <h2 className="text-lg font-semibold">
                            Document branding
                        </h2>

                        <input
                            type="file"
                            accept="image/png,image/jpeg,image/webp"
                            onChange={(e) =>
                                form.setData(
                                    "logo",
                                    e.target.files?.[0] ?? null,
                                )
                            }
                        />

                        <textarea
                            className="min-h-24 w-full rounded border p-2"
                            placeholder="Document footer"
                            value={form.data.document_footer}
                            onChange={(e) =>
                                form.setData("document_footer", e.target.value)
                            }
                        />
                    </section>

                    <button
                        disabled={form.processing}
                        className="rounded bg-slate-900 px-4 py-2 text-white"
                    >
                        Save company settings
                    </button>
                </form>
            </div>
        </>
    );
}
