# Testing Checklist

## M00

- [ ] Admin can create a user
- [ ] Unauthorized user cannot manage users
- [ ] Suspended account cannot login
- [ ] Inactive session is terminated
- [ ] User receives welcome notification
- [ ] User receives account-status notification
- [ ] Company configuration updates
- [ ] Bank account information encrypted at rest
- [ ] Document numbers increment safely
- [ ] User Excel export works
- [ ] Audit Excel export works
- [ ] Audit PDF export works
- [ ] Permissions enforce access
- [ ] Laravel auth tests pass
- [ ] TypeScript checks pass
- [ ] Production build succeeds

## M01

- [ ] Sales can create a lead
- [ ] Lead receives sequential number
- [ ] Primary contact is created
- [ ] Owner receives assignment notification
- [ ] Unauthorized users cannot manage leads
- [ ] Kanban stage movement works
- [ ] Stage probability updates
- [ ] Activity timeline works
- [ ] Follow-up activity updates due date
- [ ] Due reminder is queued
- [ ] Stale reminder is queued
- [ ] Lead converts to one client
- [ ] Contacts copied during conversion
- [ ] Lead Excel export works
- [ ] Pipeline PDF works
- [ ] Export actions appear in audit log

## M02

- [ ] Manual client creation works
- [ ] Client number is generated
- [ ] Lead conversion generates client number
- [ ] Contact can be added
- [ ] Only one primary contact is maintained
- [ ] Billing contact works
- [ ] Billing profile can be added
- [ ] Default billing profile works
- [ ] Payment terms are stored
- [ ] Private document upload works
- [ ] Secure document download works
- [ ] Client-created notification works
- [ ] Contact-added notification works
- [ ] Client Excel export works
- [ ] Client profile PDF works
- [ ] Unauthorized access returns 403
- [ ] Full PHP test suite passes
- [ ] TypeScript check passes
- [ ] Production build succeeds

## M03

- [ ] Requirement can be created
- [ ] Requirement number generated
- [ ] Version 1 created automatically
- [ ] Draft version editable
- [ ] Functional requirements persist
- [ ] Non-functional requirements persist
- [ ] Internal/client notes remain separated
- [ ] Review submission works
- [ ] Review notification queued
- [ ] Client approval email queued
- [ ] Client contact ownership validated
- [ ] Client decision can be recorded
- [ ] Approved version becomes locked
- [ ] Revision required workflow works
- [ ] New revision copies previous scope
- [ ] Version history retained
- [ ] Requirement attachments private
- [ ] SRS PDF excludes internal notes
- [ ] Estimate can be created
- [ ] Estimate totals calculate correctly
- [ ] Contingency calculates correctly
- [ ] Margin calculates correctly
- [ ] Estimate approval locks editing
- [ ] Estimate Excel works
- [ ] Estimate PDF works
- [ ] Full backend suite passes
- [ ] TypeScript check passes
- [ ] Production build passes

## M04

- [ ] Proposal requires approved requirement version
- [ ] Proposal requires approved estimate
- [ ] Proposal numbering works
- [ ] Proposal approval works
- [ ] Proposal email works
- [ ] Proposal PDF works
- [ ] Quotation numbering works
- [ ] Quotation subtotal comes from approved estimate
- [ ] Discount calculation works
- [ ] Tax calculation works
- [ ] Total calculation works
- [ ] Quotation approval works
- [ ] Quotation email works
- [ ] Quotation Excel works
- [ ] Quotation PDF works
- [ ] Acceptance/rejection recording works
- [ ] Contract requires accepted quotation
- [ ] Duplicate contract prevented
- [ ] Contract number generated
- [ ] Contract value matches quotation
- [ ] Contract issue works
- [ ] Signed state works
- [ ] Signed contract upload is private
- [ ] Internal margin never appears in client PDF
- [ ] M01-M03 regression tests pass
- [ ] Full PHP test suite passes
- [ ] TypeScript check passes
- [ ] Production build passes
- [ ] npm run check passes

## M05

- [ ] Signed contract creates project
- [ ] Unsigned contract rejected
- [ ] Duplicate project from same contract rejected
- [ ] PRJ number generated
- [ ] Client inherited from contract
- [ ] Requirement version inherited
- [ ] Estimate inherited
- [ ] Contract value inherited
- [ ] Charter version 1 created
- [ ] Draft charter editable
- [ ] Charter approval works
- [ ] Approved charter locked
- [ ] Charter revision works
- [ ] Activation requires approved charter
- [ ] Activate/hold/resume lifecycle works
- [ ] Phases work
- [ ] Milestones work
- [ ] Client milestone visibility works
- [ ] Stakeholders validated
- [ ] Risk score calculated
- [ ] Risk escalation works
- [ ] Governance decisions persist
- [ ] Kickoff email queued
- [ ] Charter PDF works
- [ ] Project Excel works
- [ ] M01-M04 regression passes
- [ ] Full PHP suite passes
- [ ] TypeScript passes
- [ ] Production build passes
- [ ] npm run check passes

## M06

- [ ] Workflow statuses seeded
- [ ] Active project creates task
- [ ] Inactive project task creation rejected
- [ ] Project task number generated
- [ ] Task assignment notification queued
- [ ] Phase ownership validated
- [ ] Milestone ownership validated
- [ ] Parent task validated
- [ ] Dependencies validated
- [ ] Self-dependency rejected
- [ ] Incomplete dependency blocks start
- [ ] Workflow transition logged
- [ ] Work submission creates version 1
- [ ] Submission moves task to Review
- [ ] Changes-required review works
- [ ] Resubmission creates next version
- [ ] Previous submission superseded
- [ ] Approval completes task
- [ ] Done blocked without approved submission
- [ ] Attachments private
- [ ] Task comments work
- [ ] Task Excel export works
- [ ] Task PDF works
- [ ] M01-M05 regression passes
- [ ] Full PHP suite passes
- [ ] TypeScript passes
- [ ] Production build passes
- [ ] npm run check passes

## M07

- [ ] Resource profile creates
- [ ] Resource profile updates
- [ ] Skills sync
- [ ] Active user assignment works
- [ ] Inactive user rejected
- [ ] Duplicate project/user assignment rejected
- [ ] Allocation validation works
- [ ] Active assignment requires active project
- [ ] PM assignment created automatically
- [ ] PM assignment activates with project
- [ ] M06 active project member may receive task
- [ ] M06 outsider task assignment rejected
- [ ] Future assignment not eligible
- [ ] End assignment works
- [ ] Assignment with open tasks cannot end
- [ ] Current PM assignment cannot end on open project
- [ ] Assignment notification queued
- [ ] Assignment-end notification queued
- [ ] Project team Excel works
- [ ] Project team PDF works
- [ ] M01-M06 regression passes
- [ ] Full PHP suite passes
- [ ] TypeScript passes
- [ ] Production build passes
- [ ] npm run check passes

## M08

- [ ] Weekly timesheet creates
- [ ] Duplicate user/week prevented
- [ ] Entry within week accepted
- [ ] Entry outside week rejected
- [ ] Project assignment validated
- [ ] Project task ownership validated
- [ ] Daily 24-hour limit enforced
- [ ] Empty timesheet submission blocked
- [ ] Missing cost rate blocks submission
- [ ] Timesheet submission works
- [ ] Review notification queued
- [ ] Rejection unlocks timesheet
- [ ] Approval locks timesheet
- [ ] Effective cost rate selected
- [ ] Hourly cost snapshot stored
- [ ] Internal cost calculated correctly
- [ ] Rate changes do not alter approved history
- [ ] Approved entries included in project cost
- [ ] Unapproved entries excluded
- [ ] Timesheet PDF works
- [ ] Timesheet Excel works
- [ ] Internal cost PDF works
- [ ] Internal cost Excel works
- [ ] Employee exports exclude internal cost
- [ ] M01-M07 regression passes
- [ ] Full PHP suite passes
- [ ] TypeScript passes
- [ ] Production build passes
- [ ] npm run check passes
