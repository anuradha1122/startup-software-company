# Business Rules

## M00

1. Public registration is disabled.
2. Admin-authorized users create system accounts.
3. Every user must have at least one role.
4. Users cannot disable or delete their own account.
5. Suspended/inactive accounts cannot authenticate.
6. Director/Admin are protected core roles.
7. Core roles cannot be deleted.
8. Company configuration is a singleton.
9. Sensitive bank information is encrypted.
10. Document numbers must never be generated with MAX()+1.
11. Number allocation must use transaction locking.
12. Administrative changes must be auditable.

## M01

1. Every lead has a stable sequential lead number.
2. Every lead belongs to one pipeline stage.
3. Lead probability follows its pipeline stage.
4. A lead may be unassigned.
5. Lead assignment sends a queued notification/email.
6. Follow-up changes reset the previous reminder state.
7. Lead activity resets stale status.
8. A lead with no activity for seven days is stale.
9. A due follow-up is notified once until rescheduled.
10. Conversion occurs inside a transaction.
11. One lead may create only one client.
12. Lead contacts are copied to client contacts.
13. Converted leads cannot be archived.
14. CRM exports are permission protected and audited.

## M02

1. Every new client receives a sequential client number.
2. A converted lead creates only one client.
3. Client conversion retains source lead history.
4. Only one primary contact is allowed per client.
5. Only one billing contact is allowed per client.
6. A client may have multiple billing profiles.
7. Only one billing profile should be marked default.
8. Payment terms are expressed in calendar days.
9. Client documents must use private storage.
10. Client documents must never be exposed through public storage URLs.
11. Every document download requires authentication and permission.
12. Client creation sends a queued internal notification/email.
13. Contact creation sends a queued internal notification/email.
14. Client export and PDF generation are audited.

## M03

1. Requirements have a stable sequential requirement number.
2. Requirement content is versioned.
3. Only draft versions may be edited.
4. Submitted/approved versions are locked.
5. Revisions create a new version number.
6. Functional and non-functional requirements are maintained separately.
7. Client-visible notes and internal notes must remain separate.
8. Internal notes must never appear in the client SRS PDF.
9. Client approval requests must target a contact belonging to the requirement client.
10. Approved requirement versions become the commercial baseline for M04.
11. Revision-required decisions preserve the previous version.
12. Requirement attachments use private storage.
13. Estimates belong to a specific requirement version.
14. Estimates are internal-only.
15. Estimate revenue equals line revenue plus contingency.
16. Internal cost is based on estimated hours multiplied by cost rate.
17. Gross margin equals estimated revenue minus estimated cost.
18. Approved estimates may not be edited.
19. Requirement and estimate exports are audited.

## M04

1. Proposals require an approved requirement version.
2. Proposals require an approved estimate belonging to that version.
3. Quotations require an approved requirement version.
4. Quotations require an approved estimate belonging to that version.
5. Proposal/quotation source scope must not be edited in M04.
6. Quotation subtotal comes from approved estimate revenue.
7. Discounts are applied before tax.
8. Internal estimate cost rates and margins must never appear on client documents.
9. Draft commercial documents may be edited.
10. Approved commercial documents are locked.
11. Only approved documents may be sent.
12. Only sent documents may receive a client decision.
13. Contracts require an accepted quotation.
14. One quotation may create only one contract.
15. Contract value equals accepted quotation total.
16. Signed-contract uploads use private storage.
17. Contract creation preserves requirement-version and estimate lineage.

## M05

1. A project requires a signed contract.
2. One contract may create only one project.
3. Client, requirement version, estimate, currency and contract value
   originate from the signed contract.
4. Commercial baseline fields cannot be manually modified in M05.
5. Every project receives a sequential project number.
6. Every new project receives charter version 1 in draft status.
7. Only draft charters may be edited.
8. Approved charters are immutable.
9. Charter revisions create a new version.
10. A project cannot activate without an approved charter.
11. Project stakeholders are governance contacts, not resource
    assignments.
12. Workforce assignments are handled by M07.
13. Risk score equals likelihood × impact.
14. Likelihood and impact are each 1-5.
15. Escalated high risks may change project health.
16. Project charter PDF must not expose internal estimate costs or
    margins.
17. Project completion requires an active/on-hold project.

## M06

1. Tasks belong to a project.
2. New tasks require an active project.
3. Task numbering is sequential within each project.
4. Task phases and milestones must belong to the same project.
5. Parent tasks must belong to the same project.
6. Dependencies must belong to the same project.
7. A task cannot depend on itself.
8. A task with incomplete dependencies cannot begin.
9. Workflow changes are recorded in task_transitions.
10. Work submissions are versioned.
11. Only one submission may await review at a time.
12. A new revision supersedes the previous submission.
13. Work submissions move tasks to Review.
14. Rejected work moves tasks to Changes Required.
15. Approved work moves tasks to Done.
16. Tasks cannot move to Done without an approved work submission.
17. Submission attachments use private storage.
18. Task assignee changes trigger notifications.
19. M06 task assignment is operational responsibility; M07 adds formal
    project-resource assignments.
20. M08 will record time against M06 tasks.

## M07

1. Only active system users may receive project assignments.
2. One user may have one assignment record per project.
3. Assignment allocation must be between 1% and 100%.
4. Active assignments require an active project.
5. Planned projects may have planned assignments.
6. Project managers receive an assignment automatically.
7. Project activation activates the planned PM assignment.
8. An M06 task assignee must have an active assignment on that project.
9. Expired or future-dated assignments are not currently assignable.
10. Users with open tasks cannot have their assignment ended.
11. An open project's current project-manager assignment cannot be ended.
12. Ending an assignment retains history.
13. Resource profiles contain operational capability information only.
14. Salary, payroll, leave, attendance, banking and HR employment fields
    belong to M15.

## M08

1. A user may have one timesheet per week.
2. Timesheet weeks run Monday through Sunday.
3. Draft/rejected timesheets may be edited.
4. Submitted/approved timesheets are locked.
5. Time entries must fall within the timesheet week.
6. Entries require a valid M07 project assignment on the work date.
7. Entries require a valid M06 task belonging to the selected project.
8. Total logged time may not exceed 24 hours per day.
9. Empty timesheets cannot be submitted.
10. Submission requires an internal cost rate for every entry date.
11. Internal cost is calculated only on approval.
12. Approved entries snapshot the effective hourly cost rate.
13. Historical cost snapshots must not change when later cost rates change.
14. Rejected timesheets may be corrected and resubmitted.
15. Only approved entries contribute to internal project cost.
16. Internal cost rates are not salary or payroll values.
17. Internal cost information must never appear in client-facing outputs.
18. M11 consumes approved M08 internal cost for profitability.

## M09

1. Expenses belong to a project.
2. Expenses may be recorded only for active/on-hold projects.
3. Expense totals are subtotal plus tax.
4. Expense amount cannot be negative.
5. Draft/rejected expenses are editable.
6. Submitted/approved expenses are locked from ordinary editing.
7. Submission requires at least one supporting attachment.
8. Rejection requires review notes.
9. Approved expenses contribute to actual non-labor project cost.
10. Draft/submitted/rejected/cancelled expenses do not contribute to
    project cost.
11. Supplier records may be optional for internal expenses.
12. Supplier-linked costs should identify the supplier whenever known.
13. Expense evidence is private.
14. Billable-to-client does not automatically create an invoice.
15. Supplier invoices and receipts are not automatically client-visible.
16. M11 combines M08 labor cost with M09 approved non-labor cost.

## M10

1. Invoices belong to a client.
2. Project invoices derive the client from the project.
3. Draft invoices may be edited.
4. Issued invoices are immutable under the ordinary edit workflow.
5. Invoice totals are recalculated from invoice lines.
6. Invoices must contain at least one line before issue.
7. Invoice totals must be greater than zero.
8. Approved M09 billable expenses may be imported explicitly.
9. M09 expenses are never invoiced automatically.
10. A single M09 expense cannot be imported into multiple invoices.
11. Expense and invoice currencies must match.
12. Issuing an invoice establishes its initial receivable balance.
13. Payments belong to a client.
14. Payment and invoice clients must match for allocation.
15. Payment and invoice currencies must match.
16. Allocation cannot exceed payment unallocated balance.
17. Allocation cannot exceed invoice outstanding balance.
18. Partial allocation sets invoice status to partially_paid unless overdue.
19. Full allocation sets invoice status to paid.
20. Past-due outstanding invoices become overdue.
21. Removing an allocation restores the outstanding invoice balance.
22. Invoice PDF contains client-facing billing data only.
23. M08 internal labor cost must never appear on invoices.
24. M09 supplier evidence/internal supplier documents must never appear on invoices.

## M11

1. Project profitability is internal-only.
2. Contracted revenue comes from the M05 project commercial baseline.
3. Labor cost comes only from approved M08 timesheet entries.
4. Non-labor cost comes only from approved M09 project expenses.
5. Invoiced revenue comes only from issued, partially-paid, paid or
   overdue M10 invoices.
6. Draft and void invoices are excluded from invoiced revenue.
7. Collected cash comes from M10 payment allocations to project invoices.
8. Unallocated client payments are not project collected cash.
9. Actual project cost equals approved labor plus approved non-labor cost.
10. Gross profit equals contracted revenue minus actual project cost.
11. Gross margin uses contracted revenue as its denominator.
12. Outstanding receivable comes from positive M10 invoice balances.
13. Currency values must not be aggregated across currencies.
14. M11 only aggregates values matching the project's base currency.
15. Currency mismatches are reported separately.
16. Financial snapshots are blocked while currency mismatches exist.
17. Portfolio totals are grouped by currency.
18. Margin warnings use project-specific warning thresholds.
19. Margin warnings are limited to one alert per project per day.
20. Daily snapshots do not modify source transactions.

## M12

1. Client portal authentication is separate from internal user authentication.
2. Portal users belong to exactly one client.
3. Portal users may access only records belonging to their client.
4. Invited users cannot sign in until password setup activates the account.
5. Suspended or revoked users cannot access the portal.
6. Portal projects are filtered by client ownership.
7. Only tasks explicitly marked client_visible may be shown.
8. Documents become visible only through explicit M12 publication.
9. Existing internal document records are not automatically portal-visible.
10. Expired portal documents cannot be downloaded.
11. Draft and void invoices are not visible to portal users.
12. Issued, partially-paid, paid and overdue invoices may be visible.
13. Client approval summaries must be written as client-safe content.
14. Internal source models are never automatically serialized to the portal.
15. Only pending approvals may be answered.
16. Rejection requires a client comment.
17. An approval may be answered only once.
18. Internal users may cancel only pending approval requests.
19. Portal responses notify the internal requester.
20. M11 profitability data must never be exposed to portal users.

## M13

1. M13 reporting is internal-only.
2. Reports read source data without modifying source transactions.
3. Project operations reporting may consume M11 profitability values.
4. Profitability values remain internal.
5. Report generation records audit metadata in report_runs.
6. M13 does not permanently store generated report files.
7. Failed report generation may be recorded with a failure message.
8. Health checks must not mutate transactional records.
9. Failed queue jobs are reported but not automatically retried/deleted.
10. Overdue invoices are reported but M10 remains responsible for invoice status.
11. Financial snapshot health is reported but M11 remains responsible for snapshots.
12. Overdue client approvals are reported but M12 remains responsible for approval workflow.
13. Operational alerts are limited to one alert cycle per day.
14. Report and operations access requires dedicated internal permissions.
15. M13 report routes must never be exposed through the client portal.

## M14

1. Support tickets belong to one client.
2. A support ticket may optionally belong to a project.
3. Project and client ownership must match.
4. Portal users may access only tickets belonging to their client.
5. Internal-only ticket notes must never appear in portal responses.
6. First response is the first non-internal internal reply.
7. Internal notes do not satisfy first-response SLA.
8. Closed/cancelled tickets cannot receive new messages.
9. Client reply to waiting_client returns the ticket to in_progress.
10. Only active users may be assigned support tickets.
11. Change requests belong to one project and its client.
12. Portal users may submit changes only for projects owned by their client.
13. Change requests require an estimate before approval.
14. Rejection requires review notes.
15. Internal change analysis is confidential.
16. Internal estimated cost is confidential.
17. Internal estimate notes are confidential.
18. Client portal may receive client_amount, estimated_days and client_notes.
19. Approved change requests may be converted to M06 delivery tasks.
20. Change approval does not automatically alter contracts, invoices or M11 revenue.

## M15

1. One User may have at most one EmployeeProfile.
2. Employee compensation is confidential.
3. Salary does not automatically create/update an M08 resource cost rate.
4. Leave requests may not overlap active leave requests.
5. Leave approval consumes leave balance.
6. Employees cannot review their own leave.
7. Rejection requires review notes.
8. Attendance and timesheets are separate concepts.
9. Duplicate daily attendance records are prohibited.
10. Employee cannot clock in twice on the same day.
11. Employee cannot clock out before clocking in.
12. Payroll uses salary snapshots.
13. Payroll recalculation is allowed only while draft/calculated.
14. Payroll approval requires calculated status.
15. Payroll publication requires approved status.
16. Published payslips are employee-private.
17. Payroll publication does not create an M08 cost transaction.
18. Overtime payment is zero until an explicit overtime-pay policy exists.
19. Unpaid-leave deduction is not inferred until an explicit payroll policy exists.
20. Payroll money calculations must avoid binary floating-point arithmetic.

## M16 Business Rules

1. A procurement request starts in `draft`.

2. A procurement request must contain at least one item before submission.

3. Only the original requester may submit their own draft procurement request.

4. A requester must not approve or reject their own procurement request.

5. Only `submitted` requests may be reviewed.

6. Review decisions are limited to `approved` and `rejected`.

7. Rejection requires review notes.

8. A purchase order may only be created from an approved procurement request.

9. Purchase-order currency must match the procurement request currency.

10. The current workflow allows only one non-cancelled purchase order per procurement request.

11. Every approved procurement item must be represented exactly once in the purchase-order creation payload.

12. Purchase-order unit pricing represents actual supplier pricing and may differ from procurement estimates.

13. Purchase orders start in `draft`.

14. Only draft purchase orders may be issued.

15. A purchase order must contain at least one item before issue.

16. Issuing a purchase order moves its procurement request to `ordered`.

17. A purchase order may not be cancelled after any goods receipt has been recorded.

18. Cancelling an unreceived purchase order returns its linked procurement request to `approved`.

19. Goods may only be received against `issued` or `partially_received` purchase orders.

20. Cumulative received quantity must never exceed ordered quantity.

21. Partial goods receipts move the purchase order to `partially_received`.

22. Full receipt moves the purchase order to `received`.

23. The linked procurement request follows the purchase order's `partially_received` or `received` state.

24. Rejected delivery quantities are recorded separately from accepted received quantities.

25. Asset registration from purchase-order items is allowed only when the purchase-order item is marked `asset_trackable`.

26. A procurement purchase cost does not automatically create an M09 project expense.

27. An asset purchase cost does not automatically create an M08 project cost.

28. A purchase order is not a client invoice.

29. Procurement approval is an internal approval and is unrelated to M12 client approval.

30. Only assets in `available` state may be assigned.

31. An asset may have only one active assignment at a time.

32. Asset assignment history must be retained.

33. Returning an asset closes the active assignment and normally makes the asset `available`.

34. Assigned assets must be returned before entering maintenance.

35. Assets may not have overlapping active maintenance records.

36. Assets in maintenance may not be assigned.

37. Active assignments must be closed before retirement.

38. Active maintenance must be completed or cancelled before retirement.

39. Retired or disposed assets must not be assigned.

40. Asset retirement requires a reason and audit actor.

41. Asset purchase cost, supplier pricing, maintenance cost, and procurement estimates are internal data.

42. Internal procurement data must not be exposed to client portal users.

43. Money values must use fixed decimal handling and must not rely on binary floating-point arithmetic.

44. Quantity calculations must use fixed decimal handling.

45. Workflow actions that can race must use transactional locking.

46. M16 notifications must be dispatched only after successful transaction commit.
