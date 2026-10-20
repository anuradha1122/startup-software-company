# Change Log

## Unreleased

### Added

- M00 Foundation & Security
- users and account status
- roles and permissions
- company settings
- encrypted bank information
- document numbering service
- audit log
- queued welcome notifications
- queued account-status notifications
- user Excel export
- audit Excel/PDF exports
- notification centre

### Added — M01

- CRM lead management
- Kanban sales pipeline
- lead source management foundation
- lead contacts
- CRM activities
- follow-up reminders
- stale lead notifications
- lead-to-client conversion
- CRM Excel export
- pipeline PDF export

### Added — M02

- Client management
- Client contacts
- Billing profiles
- Payment terms
- Client private document storage
- Client profile PDF
- Client list Excel
- Client-created notifications
- Contact-added notifications

### Added — M03

- Versioned requirement specifications
- Requirement approval workflow
- Requirement SRS PDF
- Requirement attachments
- Software estimation worksheets
- Internal cost and margin calculations
- Estimate Excel/PDF
- Requirement workflow notifications

### Added — M04

- Proposals
- Quotations
- Commercial approvals
- Discount/tax calculations
- Proposal PDF
- Quotation PDF/Excel
- Contracts
- Private signed-contract storage
- Commercial emails and notifications

### Added — M05

- Project setup
- Project charter
- Charter approval/versioning
- Project phases and milestones
- Project governance stakeholders
- Risk register
- Risk escalation
- Decision register
- Project charter PDF
- Project Excel export
- Project notifications and kickoff email

### Added — M06

- Project task workflow
- Kanban board
- Task dependencies
- Task comments
- Work submissions
- Submission review/versioning
- Private work evidence
- Task workflow notifications
- Task Excel export
- Project task PDF report

### Added — M07

- Resource profiles
- Skills catalogue
- Project resource assignments
- Assignment lifecycle
- Allocation percentage
- Project manager auto-assignment
- Task assignment eligibility
- Project team PDF
- Project team Excel
- Assignment notifications

### Added — M08

- Weekly timesheets
- Project task time entries
- Timesheet approval workflow
- Effective-dated internal cost rates
- Historical internal cost snapshots
- Internal project labor costing
- Timesheet PDF/Excel
- Internal cost PDF/Excel
- Timesheet notifications

### Added — M09

- Supplier management
- Project expenses
- Supplier-linked project costs
- Private receipts/invoices
- Expense approval workflow
- Approved non-labor costing
- Expense PDF/Excel
- Non-labor cost PDF/Excel
- Expense notifications

### Added — M10

- Client invoices
- Invoice line calculation
- Billable M09 expense import
- Invoice PDF/email
- Client payments
- Payment allocations
- Payment receipt PDF/email
- Outstanding receivables
- Overdue invoice detection
- Invoice/payment/receivable Excel exports

### Added — M11

- Project profitability reporting
- Revenue/cost consolidation
- Gross profit and gross margin
- Unbilled revenue
- Collection metrics
- Currency-integrity checking
- Financial snapshots
- Margin threshold configuration
- Margin alerts
- Project profitability PDF/Excel
- Finance portfolio PDF/Excel

### Added — M12

- Client portal authentication
- Client portal user administration
- Portal password setup
- Client project dashboard
- Published client documents
- Client invoice access
- Client approval workflow
- Approval notifications
- Approval PDF/Excel
- Client project summary PDF

### Added — M13

- Internal Report Center
- Project Operations report
- Cross-module operational reporting
- Report generation audit records
- Report history exports
- Admin Operations dashboard
- Operational health checking
- Scheduled health notifications

### Added — M14

- Support tickets
- Support conversations
- Support assignment/SLA
- Client portal support
- Change request workflow
- Change request estimates
- Internal costing separation
- Change approval/rejection
- M06 task conversion
- Support/change Excel exports

### Added — M15

- Employee HR records
- Compensation history
- Leave management
- Attendance
- Payroll
- Payslips
- HR permissions
- Employee self service

## M16 — Procurement & Asset Management

### Added

- ProcurementRequest model and workflow
- ProcurementRequestItem model
- procurement submission
- procurement approval/rejection
- self-approval prevention
- PurchaseOrder model
- PurchaseOrderItem model
- PO issue/cancel workflow
- supplier PO linkage
- purchase-order PDF
- GoodsReceipt model
- GoodsReceiptItem model
- partial goods receipts
- cumulative received quantity tracking
- over-receipt prevention
- Asset model
- asset registration
- asset tagging
- asset assignment
- assignment history
- asset returns
- AssetMaintenance model
- maintenance lifecycle
- asset retirement
- ProcurementMoneyService
- ProcurementQuantityService
- M16 Excel exports
- M16 database notifications
- M16 React/Inertia pages
- M16 Wayfinder routes/actions
- M16 feature tests

### Changed

- M16 asset return workflow uses `returnAsset()` instead of `return()`.
- Purchase-order creation now requires explicit line pricing.
- Purchase-order items explicitly mark whether assets should be tracked.
- Procurement and asset notifications are dispatched after transaction commit.
- M16 frontend actions use Wayfinder-generated routes.

### Security

- M16 export endpoints require explicit permissions.
- Procurement/supplier pricing remains internal.
- M16 data is excluded from client portal exposure.
- Raw model serialization is avoided for sensitive frontend payloads.

### Status

M16 is not yet marked final until all project gates are green.
