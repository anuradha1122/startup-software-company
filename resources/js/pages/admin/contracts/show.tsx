import { Head, router, useForm } from '@inertiajs/react';

export default function ContractShow({ contract }: any) {
    const documentForm = useForm({
        type: 'signed_contract',
        title: '',
        file: null as File | null,
    });

    return (
        <>
            <Head title={contract.contract_number} />

            <div className="max-w-6xl p-6">
                <div className="mb-6 flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {contract.contract_number}
                        </h1>

                        <p>{contract.title}</p>

                        <p>
                            {contract.currency} {contract.contract_value}
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href={`/admin/contracts/${contract.public_id}/pdf`}
                            className="rounded border px-3 py-2"
                        >
                            PDF
                        </a>

                        {contract.status === 'draft' && (
                            <button
                                onClick={() =>
                                    router.post(
                                        `/admin/contracts/${contract.public_id}/issue`,
                                    )
                                }
                                className="rounded bg-primary px-3 py-2 text-primary-foreground"
                            >
                                Issue Contract
                            </button>
                        )}

                        {contract.status === 'issued' && (
                            <button
                                onClick={() =>
                                    router.post(
                                        `/admin/contracts/${contract.public_id}/signed`,
                                    )
                                }
                                className="rounded border px-3 py-2"
                            >
                                Mark Signed
                            </button>
                        )}
                    </div>
                </div>

                <section className="rounded border p-5">
                    <dl className="grid gap-4 md:grid-cols-2">
                        <div>
                            <dt>Client</dt>
                            <dd>{contract.client?.name}</dd>
                        </div>

                        <div>
                            <dt>Quotation</dt>
                            <dd>{contract.quotation?.quotation_number}</dd>
                        </div>

                        <div>
                            <dt>Start Date</dt>
                            <dd>{contract.start_date}</dd>
                        </div>

                        <div>
                            <dt>End Date</dt>
                            <dd>{contract.end_date}</dd>
                        </div>
                    </dl>
                </section>

                <section className="mt-6 rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Contract Documents
                    </h2>

                    <form
                        className="grid gap-3 md:grid-cols-3"
                        onSubmit={(event) => {
                            event.preventDefault();

                            documentForm.post(
                                `/admin/contracts/${contract.public_id}/documents`,
                                {
                                    forceFormData: true,
                                },
                            );
                        }}
                    >
                        <select
                            className="rounded border p-2"
                            value={documentForm.data.type}
                            onChange={(e) =>
                                documentForm.setData('type', e.target.value)
                            }
                        >
                            <option value="signed_contract">
                                Signed Contract
                            </option>

                            <option value="addendum">Addendum</option>

                            <option value="supporting_document">
                                Supporting Document
                            </option>
                        </select>

                        <input
                            className="rounded border p-2"
                            placeholder="Title"
                            value={documentForm.data.title}
                            onChange={(e) =>
                                documentForm.setData('title', e.target.value)
                            }
                        />

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
                            Upload
                        </button>
                    </form>

                    <div className="mt-5 space-y-2">
                        {contract.documents.map((document: any) => (
                            <div
                                key={document.public_id}
                                className="flex justify-between rounded border p-3"
                            >
                                <div>
                                    <p>{document.title}</p>

                                    <p className="text-sm text-muted-foreground">
                                        {document.type}
                                    </p>
                                </div>

                                <a
                                    href={`/admin/contracts/${contract.public_id}/documents/${document.public_id}/download`}
                                    className="underline"
                                >
                                    Download
                                </a>
                            </div>
                        ))}
                    </div>
                </section>
            </div>
        </>
    );
}
