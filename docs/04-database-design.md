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
