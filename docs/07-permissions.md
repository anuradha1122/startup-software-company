# Roles & Permissions

## Core Roles

- Director
- Admin
- Sales
- Project Manager
- Finance
- HR
- Employee
- Freelancer
- Client

## M00 Permissions

- users.view
- users.create
- users.update
- users.delete
- users.export
- roles.manage
- company-settings.view
- company-settings.update
- audit.view
- audit.export

Director and Admin receive every M00 permission.

Permissions are assigned to roles rather than directly to users.

## M01 Permissions

- leads.view
- leads.create
- leads.update
- leads.delete
- leads.assign
- leads.convert
- leads.export

Director/Admin:
all M01 permissions.

Sales:
view, create, update, assign, convert, export.

Project Manager:
view and export.

## M02 Permissions

- clients.view
- clients.create
- clients.update
- clients.delete
- clients.export
- clients.contacts.manage
- clients.billing.manage
- clients.documents.manage
- clients.documents.download

## M03 Permissions

Requirements:

- requirements.view
- requirements.create
- requirements.update
- requirements.review
- requirements.request-approval
- requirements.record-approval
- requirements.export
- requirements.attachments.manage
- requirements.attachments.download

Estimation:

- estimates.view
- estimates.create
- estimates.update
- estimates.approve
- estimates.export
