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
