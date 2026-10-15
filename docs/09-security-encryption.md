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
