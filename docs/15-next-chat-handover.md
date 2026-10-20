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

## Current Module

M09 — Expenses & Supplier Costs

## Completion Gate

Before M10:

1. supplier numbering tested
2. supplier CRUD tested
3. expense numbering tested
4. expense calculation tested
5. project-state validation tested
6. private attachments tested
7. expense submission tested
8. approval tested
9. rejection tested
10. approved-expense lock tested
11. non-labor-cost aggregation tested
12. expense PDF tested
13. expense Excel tested
14. non-labor-cost PDF tested
15. non-labor-cost Excel tested
16. notifications tested
17. M01-M08 regressions pass
18. full PHP suite passes
19. TypeScript passes
20. production build passes
21. npm run check passes
22. M09 Git commit created

## Next Module

M10 — Invoices, Payments & Receivables

M10 will introduce client invoices, invoice lines, payment recording,
outstanding receivables and collection status.

M10 may use approved M09 billable-to-client costs as invoice inputs,
but such expenses must never be invoiced automatically.

## Current Module

M10 — Invoices, Payments & Receivables

## Completion Gate

Before M11:

1. invoice numbering tested
2. draft invoice creation tested
3. invoice calculations tested
4. issue workflow tested
5. invoice locking tested
6. invoice PDF tested
7. invoice email tested
8. M09 billable expense import tested
9. duplicate expense billing prevented
10. payment numbering tested
11. payment creation tested
12. partial allocation tested
13. full allocation tested
14. cross-client allocation rejected
15. currency mismatch rejected
16. over-allocation rejected
17. allocation removal tested
18. receipt PDF tested
19. receipt email tested
20. receivables tested
21. overdue command tested
22. exports tested
23. M01-M09 regressions pass
24. full PHP suite passes
25. TypeScript passes
26. production build passes
27. npm run check passes
28. M10 Git commit created

## Next Module

M11 — Project Profitability & Finance

M11 will combine:

- project contract/revenue baseline
- M08 approved internal labor cost
- M09 approved non-labor cost
- M10 invoiced revenue
- M10 collected cash

to produce project-level revenue, cost, gross profit, margin,
receivable and collection reporting.

## Current Module

M11 — Project Profitability & Finance

## Completion Gate

Before M12:

1. M05 contract baseline tested
2. M08 approved labor integration tested
3. M09 approved cost integration tested
4. M10 invoice integration tested
5. M10 payment-allocation integration tested
6. gross-profit calculation tested
7. margin calculation tested
8. receivable calculation tested
9. collection percentage tested
10. currency mismatch tested
11. snapshots tested
12. snapshot currency gate tested
13. portfolio currency grouping tested
14. margin settings tested
15. margin alerts tested
16. profitability PDF tested
17. profitability Excel tested
18. finance PDF tested
19. finance Excel tested
20. M01-M10 regressions pass
21. full PHP suite passes
22. Pint passes
23. TypeScript passes
24. production build passes
25. npm run check passes
26. M11 Git commit created

## Next Module

M12 — Client Portal & Approvals

M12 must expose only explicitly client-safe information.

M11 internal profitability, internal labor cost, supplier cost, gross
profit and gross margin must never be exposed to M12.

## Current Module

M12 — Client Portal & Approvals

## Completion Gate

Before M13:

1. portal invitation tested
2. password setup tested
3. portal login tested
4. suspension/revocation tested
5. cross-client isolation tested
6. project safety mapping tested
7. client-visible tasks tested
8. document publishing tested
9. document expiry tested
10. portal invoice access tested
11. draft invoice protection tested
12. approval request tested
13. approval response tested
14. duplicate response protection tested
15. rejection comment tested
16. approval notifications tested
17. approval PDF tested
18. approval Excel tested
19. project summary PDF tested
20. M11/internal finance leakage test passes
21. M01-M11 regressions pass
22. full PHP suite passes
23. Pint passes
24. TypeScript passes
25. production build passes
26. npm run check passes
27. M12 Git commit created

## Next Module

M13 — Reports, Exports & Admin Operations

M13 will consolidate internal reporting, administrative exports,
operational health/status tools and cross-module administration.

M13 must maintain the M12 client-portal security boundary.

## Current Module

M13 — Reports, Exports & Admin Operations

## Completion Gate

Before M14:

1. report center tested
2. report permissions tested
3. project operations report tested
4. project operations Excel tested
5. project operations PDF tested
6. report run audit tested
7. report run history tested
8. report run Excel tested
9. report run PDF tested
10. database health check tested
11. failed queue job health check tested
12. overdue invoice check tested
13. overdue client approval check tested
14. finance snapshot health tested
15. manual health check tested
16. scheduled health command tested
17. health notification tested
18. duplicate alert suppression tested
19. client portal isolation from M13 tested
20. M01-M12 regressions pass
21. full PHP suite passes
22. Pint passes
23. TypeScript passes
24. production build passes
25. npm run check passes
26. M13 Git commit created

## Next Module

M14 — Support & Change Requests

M14 will introduce:

- support tickets
- ticket conversations
- priority/severity
- assignment
- SLA dates
- client portal support access
- project-linked change requests
- effort/cost estimation
- approval workflow
- conversion of approved change requests into delivery work

M14 must preserve the M12 client-data boundary and must not expose
M11/M13 internal financial information to portal users.

## Current Module

M14 — Support & Change Requests

## Completion Gate

Before M15:

1. support numbering tested
2. internal ticket creation tested
3. portal ticket creation tested
4. assignment tested
5. first-response SLA tested
6. resolution SLA tested
7. client reply tested
8. internal reply tested
9. internal note privacy tested
10. ticket transitions tested
11. cross-client ticket access tested
12. change numbering tested
13. internal change creation tested
14. portal change creation tested
15. project ownership tested
16. estimate tested
17. internal estimate privacy tested
18. approval tested
19. rejection tested
20. task conversion tested
21. support export tested
22. change export tested
23. M01-M13 regressions pass
24. full PHP suite passes
25. Pint passes
26. TypeScript passes
27. production build passes
28. npm run check passes
29. M14 Git commit created

## Next Module

M15 — HR, Leave, Attendance & Payroll

M15 will introduce employee HR records, leave management, attendance and
payroll.

M15 salary/payroll data must remain separate from M07 resource profiles
and M08 operational internal costing.

## Current Module

M15 — HR, Leave, Attendance & Payroll

## Completion Gate

Before M16:

1. employee creation tested
2. employee privacy tested
3. compensation history tested
4. compensation permission tested
5. leave balance tested
6. leave creation tested
7. overlap guard tested
8. self approval guard tested
9. leave approval tested
10. leave rejection tested
11. attendance clock-in tested
12. duplicate clock-in tested
13. attendance clock-out tested
14. payroll money precision tested
15. payroll calculation tested
16. salary snapshot tested
17. payroll approval tested
18. payroll publication tested
19. payslip privacy tested
20. payslip PDF tested
21. employee/leave/attendance/payroll exports tested
22. M01-M14 regressions pass
23. full PHP suite passes
24. Pint passes
25. TypeScript passes
26. production build passes
27. npm run check passes
28. M15 Git commit created

## Next Module

M16 — Procurement & Asset Management

M16 will introduce suppliers/procurement requests, purchase orders,
company assets, asset assignment and asset lifecycle.

M16 must keep procurement purchase cost internal and separate from
client billing unless explicitly approved for M09/M10 workflows.

# Current Handover — M16 Procurement & Asset Management

## Current Module

M16 — Procurement & Asset Management

## Status

In Progress.

Do not mark M16 complete or commit it as complete until all targeted, regression, backend, frontend, and formatting gates are green.

## Main Workflow

Procurement Request
→ Submission
→ Internal Review
→ Approval
→ Purchase Order
→ PO Issue
→ Goods Receipt
→ Asset Registration
→ Assignment
→ Return / Maintenance
→ Retirement

## M16 Models

- ProcurementRequest
- ProcurementRequestItem
- PurchaseOrder
- PurchaseOrderItem
- GoodsReceipt
- GoodsReceiptItem
- Asset
- AssetAssignment
- AssetMaintenance

## M16 Services

### Procurement

- ProcurementMoneyService
- ProcurementQuantityService
- ProcurementRequestService
- ProcurementReviewService
- PurchaseOrderService
- PurchaseOrderWorkflowService
- GoodsReceiptService

### Assets

- AssetService
- AssetAssignmentService
- AssetMaintenanceService
- AssetLifecycleService

## Important Boundaries

- salary != M08 resource cost rate;
- payroll != procurement;
- PO != client invoice;
- goods receipt != payment;
- purchase cost != automatic M09 expense;
- asset purchase cost != automatic M08 project cost;
- procurement approval != client approval.

M16 internal prices and costs must not be exposed to client portal users.

## Important Implementation Rules

### Supplier Tests

The existing M09 `Supplier` model does not currently provide `Supplier::factory()`.

M16 tests should use the existing supplier creation pattern or `tests/Feature/M16/Helpers.php`.

Do not modify production Supplier behavior merely to satisfy M16 tests unless a Supplier factory is intentionally being introduced project-wide.

### Asset Return

The canonical method is:

php
AssetAssignmentService::returnAsset()
