type Option = {
    id: number;
    name: string;
};

type LeadFormData = {
    title: string;
    company_name: string;
    stage_id: string;
    source_id: string;
    owner_id: string;
    estimated_value: string;
    currency: string;
    priority: string;
    next_follow_up_at: string;
    notes: string;
};

type Props = {
    data: LeadFormData;
    setData: (key: keyof LeadFormData, value: any) => void;
    stages: Option[];
    sources: Option[];
    owners: Option[];
};

export default function LeadForm({
    data,
    setData,
    stages,
    sources,
    owners,
}: Props) {
    return (
        <div className="grid gap-4 md:grid-cols-2">
            <label className="space-y-1">
                <span>Lead title</span>
                <input
                    className="w-full rounded border p-2"
                    value={data.title}
                    onChange={(e) => setData("title", e.target.value)}
                />
            </label>

            <label className="space-y-1">
                <span>Company</span>
                <input
                    className="w-full rounded border p-2"
                    value={data.company_name}
                    onChange={(e) => setData("company_name", e.target.value)}
                />
            </label>

            <label className="space-y-1">
                <span>Stage</span>
                <select
                    className="w-full rounded bg-white dark:bg-gray-950 border p-2"
                    value={data.stage_id}
                    onChange={(e) => setData("stage_id", e.target.value)}
                >
                    {stages.map((stage) => (
                        <option key={stage.id} value={stage.id}>
                            {stage.name}
                        </option>
                    ))}
                </select>
            </label>

            <label className="space-y-1">
                <span>Source</span>
                <select
                    className="w-full rounded bg-white dark:bg-gray-950 border p-2"
                    value={data.source_id}
                    onChange={(e) => setData("source_id", e.target.value)}
                >
                    <option value="">Select source</option>

                    {sources.map((source) => (
                        <option key={source.id} value={source.id}>
                            {source.name}
                        </option>
                    ))}
                </select>
            </label>

            <label className="space-y-1">
                <span>Owner</span>
                <select
                    className="w-full rounded bg-white dark:bg-gray-950 border p-2"
                    value={data.owner_id}
                    onChange={(e) => setData("owner_id", e.target.value)}
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
                <span>Priority</span>
                <select
                    className="w-full rounded border bg-white dark:bg-gray-950 p-2"
                    value={data.priority}
                    onChange={(e) => setData("priority", e.target.value)}
                >
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </label>

            <label className="space-y-1">
                <span>Estimated value</span>
                <input
                    type="number"
                    min="0"
                    step="0.01"
                    className="w-full rounded border p-2"
                    value={data.estimated_value}
                    onChange={(e) => setData("estimated_value", e.target.value)}
                />
            </label>

            <label className="space-y-1">
                <span>Currency</span>
                <input
                    className="w-full rounded border p-2"
                    value={data.currency}
                    maxLength={3}
                    onChange={(e) =>
                        setData("currency", e.target.value.toUpperCase())
                    }
                />
            </label>

            <label className="space-y-1 md:col-span-2">
                <span>Next follow-up</span>
                <input
                    type="datetime-local"
                    className="w-full rounded border p-2"
                    value={data.next_follow_up_at}
                    onChange={(e) =>
                        setData("next_follow_up_at", e.target.value)
                    }
                />
            </label>

            <label className="space-y-1 md:col-span-2">
                <span>Notes</span>

                <textarea
                    className="min-h-28 w-full rounded border p-2"
                    value={data.notes}
                    onChange={(e) => setData("notes", e.target.value)}
                />
            </label>
        </div>
    );
}
