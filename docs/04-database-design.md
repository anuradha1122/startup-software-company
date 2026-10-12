# Database Design

## M00 Tables

- users
- roles
- permissions
- model_has_roles
- model_has_permissions
- role_has_permissions
- company_settings
- document_sequences
- activity_log
- notifications
- jobs
- failed_jobs
- cache
- sessions

## Users

Public reference: ULID `public_id`.

Status values:

- active
- inactive
- suspended

## Company Settings

Bank account number and SWIFT code are encrypted at the model layer.

## Document Sequences

Document numbers must be allocated inside a database transaction with
a row lock to prevent duplicate numbers.

## M01 Tables

- lead_stages
- lead_sources
- leads
- lead_contacts
- lead_activities
- clients
- client_contacts

### Conversion Rule

A lead may create only one client.

`clients.source_lead_id` is unique.

Lead contact records are copied to client contacts during conversion
inside one database transaction.

M02 extends client records rather than rebuilding them.

## M02 Tables

Extended:

- clients
- client_contacts

Added:

- client_billing_profiles
- client_documents

### Client Documents

Client documents use the private Laravel `local` disk.

Files are downloaded only through authenticated and permission-
protected controller routes.

### Billing Profiles

A client may hold multiple billing profiles, but only one should be
marked as default.

Payment terms are stored as number of days.
