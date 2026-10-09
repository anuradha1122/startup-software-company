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
