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
