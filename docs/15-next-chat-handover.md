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

## Current Module

M04 — Proposal, Quotation & Contract

## Completion Gate

Before M05:

1. proposal generation tested
2. approved-source validation tested
3. quotation calculations verified
4. proposal PDF checked
5. quotation PDF checked
6. quotation Excel checked
7. send workflow checked
8. acceptance/rejection checked
9. accepted quotation to contract tested
10. duplicate contract prevention tested
11. contract PDF checked
12. issue/sign workflow checked
13. private contract documents checked
14. queued emails checked
15. full PHP suite passes
16. TypeScript check passes
17. production build passes
18. npm run check passes
19. M04 Git commit created

## Next Module

M05 — Project Setup & Governance

M05 may create a project only from the approved commercial baseline,
normally a signed contract backed by an accepted quotation.

## Current Module

M05 — Project Setup & Governance

## Completion Gate

Before M06:

1. signed-contract validation passes
2. duplicate-project prevention passes
3. project numbering passes
4. commercial baseline integrity verified
5. charter versioning tested
6. charter approval tested
7. activation gate tested
8. lifecycle tested
9. phases tested
10. milestones tested
11. stakeholder validation tested
12. risk scoring tested
13. escalation tested
14. decision register tested
15. kickoff notification tested
16. charter PDF checked
17. Excel export checked
18. M01-M04 regression tests pass
19. full PHP suite passes
20. TypeScript passes
21. production build passes
22. npm run check passes
23. M05 Git commit created

## Next Module

M06 — Tasks, Workflow & Work Submission

M06 must operate inside an active M05 project and may use project
phases and milestones as task/workflow grouping structures.

## Current Module

M06 — Tasks, Workflow & Work Submission

## Completion Gate

Before M07:

1. workflow statuses seeded
2. task numbering tested
3. active-project gate tested
4. task relation ownership tested
5. dependency blocking tested
6. workflow transitions tested
7. task comments tested
8. work submission tested
9. submission versioning tested
10. changes-required workflow tested
11. approval workflow tested
12. task completion gate tested
13. private attachments tested
14. queued notifications tested
15. Excel export checked
16. PDF export checked
17. M01-M05 regressions pass
18. full PHP suite passes
19. TypeScript passes
20. production build passes
21. npm run check passes
22. M06 Git commit created

## Next Module

M07 — People & Assignments

M07 will formalize who may participate in a project and will connect
project-resource assignments to M06 task responsibility.

Existing M06 assignee_id remains compatible and M07 should validate
assignees against active project assignments.

## Current Module

M07 — People & Assignments

## Completion Gate

Before M08:

1. resource profile tested
2. skills tested
3. project assignment tested
4. duplicate assignment guard tested
5. active-project guard tested
6. PM auto-assignment tested
7. PM activation tested
8. M06 task-assignee eligibility tested
9. outsider task assignment rejected
10. assignment-end guard tested
11. notification queue tested
12. project team PDF checked
13. assignment Excel checked
14. M01-M06 regression tests pass
15. full PHP suite passes
16. TypeScript passes
17. production build passes
18. npm run check passes
19. M07 Git commit created

## Next Module

M08 — Timesheets & Internal Costing

M08 will record actual work time against M06 tasks and M07 project
assignments.

Internal costing must remain private and must not be exposed on client
documents or portal screens.

## Current Module

M08 — Timesheets & Internal Costing

## Completion Gate

Before M09:

1. weekly timesheet creation tested
2. project assignment validation tested
3. project task validation tested
4. work-date validation tested
5. daily-hour limit tested
6. cost-rate period validation tested
7. submission workflow tested
8. approval workflow tested
9. rejection/resubmission tested
10. historical cost snapshot tested
11. approved-project-cost aggregation tested
12. timesheet PDF checked
13. timesheet Excel checked
14. internal cost PDF checked
15. internal cost Excel checked
16. employee export privacy checked
17. queued notifications tested
18. M01-M07 regressions pass
19. full PHP suite passes
20. TypeScript passes
21. production build passes
22. npm run check passes
23. M08 Git commit created

## Next Module

M09 — Expenses & Supplier Costs

M09 will add non-labor project costs.

M11 profitability will later combine:

- M08 approved internal labor cost
- M09 approved expense/supplier cost
- M10 invoice/payment revenue
