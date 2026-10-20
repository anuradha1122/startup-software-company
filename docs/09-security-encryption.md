# Security & Encryption

## Authentication

Laravel Fortify.

Enabled:

- password authentication
- email verification
- password reset
- 2FA
- password confirmation

Public registration disabled.

## Authorization

Server-side Laravel policies and permissions are mandatory.

Frontend permission checks are display convenience only.

## Encryption

Encrypted:

- company bank account number
- SWIFT code
- authentication secrets provided by Laravel/Fortify

Never store plaintext user passwords.

## Sessions

Database sessions with session encryption enabled.

## Files

Contracts, receipts, HR records and similar files must use private
storage.

Temporary/signed download routes will be implemented by modules that
introduce those files.

## Audit

Authentication and protected model changes are recorded.

## M02 Client Documents

Client documents are stored on the private local disk.

Access is provided only through authenticated controller downloads.

Document upload/download activity is audited.

Client documents must not be exposed using `storage:link`.

## M03 Security

Requirement attachments use private Laravel storage.

Client-facing SRS documents exclude internal notes and internal
estimation data.

Estimation worksheets are internal-only.

Client approval contacts must belong to the requirement's client.

Approved requirement versions and estimates are locked from editing.

## M04 Security

Commercial documents exclude:

- cost rates
- estimated internal cost
- gross margin
- internal estimation notes

Signed contracts and supporting documents use private storage.

Downloads are permission protected.

Commercial status changes and exports are audited.

## M05 Security

Project commercial baseline references are immutable.

Project charter exports exclude:

- internal estimate cost
- cost rates
- gross margin
- internal estimation notes

Governance actions use permission gates.

Risk escalations and project lifecycle changes are audit logged.

## M06 Security

Work submission evidence uses private storage.

Attachment downloads require explicit permissions.

Tasks may contain internal project information and are not exposed
through public URLs.

Client-visible task/comment flags prepare data for M12 but do not
automatically publish data.

Workflow transitions and reviews are audit logged.

## M07 Security

M07 stores operational resource information only.

It does not store:

- salary
- bank details
- payroll data
- attendance
- leave records
- private HR records

People and assignment mutations require explicit permissions.

Project team exports are audited.

## M08 Security

Internal hourly cost and project labor cost are confidential internal
finance information.

They must not appear in:

- proposals
- quotations
- contracts
- client-facing project PDFs
- client portal data
- employee-facing timesheet exports

Access requires internal-cost permissions.

Cost-rate changes and cost exports are audited.

## M09 Security

Receipts, supplier invoices, quotations and payment evidence use
private storage.

Downloads require explicit expense attachment permissions.

Supplier and project-cost exports are audited.

Internal supplier pricing and receipts must not be exposed through the
client portal unless explicitly approved in a future workflow.

## M10 Security

Invoice PDFs and client emails contain client-facing commercial data.

They must never contain:

- M08 internal hourly rates
- M08 internal labor cost
- M09 supplier pricing evidence
- supplier invoices
- internal profitability
- internal margin data

Payment and receivable access requires explicit billing permissions.

Invoice/payment exports are internal administrative reports.

## M11 Security

Project profitability is confidential internal financial information.

It must never be exposed through:

- client portal
- client invoice
- proposal
- quotation
- contract PDF
- client project reports
- client-facing email

Restricted values include:

- internal labor cost
- internal hourly rates
- supplier cost
- gross profit
- gross margin
- cost consumption
- profitability snapshots

Exports require dedicated profitability/finance permissions and are
audit logged.

## M12 Security

M12 creates a strict client-facing security boundary.

Portal users use:

- separate auth guard
- separate auth provider
- separate password-reset broker
- client-scoped authorization
- active-account middleware

Portal documents remain on private storage.

Every portal project, approval, document and invoice access must verify
client ownership.

Never expose:

- internal labor rates/costs
- supplier evidence
- profitability
- margin
- internal notes
- workforce allocations
- internal audit data
- bank secrets

## M13 Security

M13 reports may aggregate sensitive internal commercial and operational
information.

Sensitive values include:

- labor cost
- non-labor cost
- gross profit
- gross margin
- outstanding receivable
- project staffing information
- operational queue health

M13 routes require explicit internal permissions.

M13 data must never be exposed through M12 portal controllers or portal
Inertia payloads.

Report exports and manual health checks are activity logged.

## M14 Security

Portal support DTOs exclude:

- internal support notes
- internal user-only diagnostics
- internal assignment details not intended for the client

Portal change-request DTOs exclude:

- internal_analysis
- internal_estimated_cost
- internal_notes
- M08 internal cost
- M09 supplier cost
- M11 profitability/margin
- M13 operational information

Every portal support and change-request endpoint verifies client
ownership.

## M15 Security

Salary and payroll are confidential HR records.

Never expose M15 compensation through:

- client portal
- project profitability DTOs
- generic project reports
- resource allocation pages
- timesheet pages
- support tickets
- change requests

Employee self-service may access only the authenticated employee's
leave, attendance and published payslips.

Compensation management requires dedicated permissions.

## M16 Security and Data Classification

M16 contains internal commercial and operational information.

### Sensitive M16 Data

Sensitive internal data includes:

- supplier prices;
- procurement estimates;
- purchase-order unit prices;
- purchase-order totals;
- asset purchase costs;
- maintenance costs;
- supplier references;
- internal procurement notes;
- internal review notes.

### Access Controls

Every M16 endpoint must require:

- authentication;
- appropriate Spatie permission;
- project or organizational scope where applicable.

Frontend visibility is not authorization.

Backend authorization must always be enforced.

### Client Portal Boundary

M12 client portal users must never receive:

- supplier details not intended for clients;
- supplier pricing;
- procurement request estimates;
- procurement review notes;
- purchase orders;
- goods receipt details;
- internal asset purchase costs;
- maintenance costs.

### DTO Requirement

Admin Inertia responses must use explicit data structures rather than unrestricted serialized models.

This reduces accidental leakage of:

- hidden model fields;
- internal foreign keys;
- unrelated employee fields;
- supplier financial data;
- internal notes.

### Route Identifiers

Externally exposed admin URLs should use `public_id` rather than numeric primary keys where supported.

### Transaction Security

Approval, purchase-order, goods-receipt, assignment, maintenance, and retirement transitions are transaction-protected.

### Audit Considerations

Purchase costs and procurement amounts should not be exposed through generic activity-log interfaces unless the viewer has suitable procurement/asset permissions.

### Export Security

Excel and PDF endpoints require explicit permissions.

Exports must not bypass the same data-scope rules that apply to normal UI access.
