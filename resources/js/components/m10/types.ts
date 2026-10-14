export type ClientSummary = {
    id: number;
    public_id: string;
    name: string;
    default_currency: string;
};

export type InvoiceSummary = {
    id: number;
    public_id: string;
    invoice_number: string;
    status: string;
    invoice_date: string;
    due_date: string;
    currency: string;
    total_amount: string;
    amount_paid: string;
    balance_due: string;
    client: {
        name: string;
    };
    project?: {
        project_number: string;
        name: string;
    } | null;
};

export type InvoiceLine = {
    id: number;
    public_id: string;
    description: string;
    quantity: string;
    unit_price: string;
    tax_rate: string;
    subtotal: string;
    tax_amount: string;
    total_amount: string;
    source_type?: string | null;
    source_id?: number | null;
};
