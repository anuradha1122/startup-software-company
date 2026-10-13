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
