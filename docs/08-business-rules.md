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
