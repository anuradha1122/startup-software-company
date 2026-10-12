# Next Chat Handover

## Current Module

M00 — Foundation & Security

## Stack

Laravel 13 + React + Inertia + MySQL.

## M00 Completion Gate

Before moving to M01:

1. migrations pass
2. seeders pass
3. PHP tests pass
4. frontend type checks pass
5. frontend production build passes
6. user/role/company/audit UI manually checked
7. Excel download checked
8. PDF download checked
9. queued email/notification checked
10. M00 Git commit created

## Next Module

M01 — CRM & Leads

Do not begin M01 until the M00 gate is green.

## Current Module

M01 — CRM & Leads

## M01 Completion Gate

Before M02:

1. M01 migrations pass
2. lead seeders pass
3. CRUD manually tested
4. Kanban drag/drop tested
5. lead assignment email tested
6. follow-up reminder tested
7. stale reminder tested
8. lead conversion tested
9. Lead Excel checked
10. Pipeline PDF checked
11. php artisan test passes
12. TypeScript checks pass
13. production build passes
14. M01 Git commit created

## Next Module

M02 — Clients & Contacts

M02 must extend the clients/client_contacts foundation created by M01.

## Current Module

M02 — Clients & Contacts

## Completion Gate

Before M03:

1. migrations pass
2. seeders pass
3. client CRUD tested
4. lead conversion tested
5. contacts tested
6. billing profiles tested
7. payment terms tested
8. private upload/download tested
9. queued notifications/emails tested
10. client PDF tested
11. client Excel tested
12. php artisan test passes
13. TypeScript checks pass
14. production build passes
15. M02 Git commit created

## Next Module

M03 — Requirements & Estimation

## Current Module

M03 — Requirements & Estimation

## Completion Gate

Before M04:

1. migrations pass
2. requirement numbering works
3. versioning tested
4. approved-version locking tested
5. client approval workflow tested
6. revision workflow tested
7. attachments tested
8. SRS PDF checked
9. estimation totals checked
10. margin checked
11. estimate approval checked
12. Excel checked
13. estimate PDF checked
14. notification/email flow checked
15. php artisan test passes
16. TypeScript checks pass
17. production build passes
18. npm run check passes
19. M03 Git commit created

## Next Module

M04 — Proposal, Quotation & Contract

M04 must consume an approved requirement version and an approved
estimate rather than duplicating requirement or estimation data.
