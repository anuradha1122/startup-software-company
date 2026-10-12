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
