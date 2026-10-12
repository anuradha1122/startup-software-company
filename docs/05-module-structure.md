# Module Structure

## M00

Backend:

- app/Models
- app/Http/Controllers/Admin
- app/Http/Requests/Admin
- app/Policies
- app/Notifications
- app/Exports
- app/Services

Frontend:

- resources/js/pages/admin
- resources/js/pages/notifications
- resources/js/components/m00

Routes:

- routes/web.php
- routes/admin.php

## M01

Backend:

- Models/Lead\*
- Models/Client\*
- Controllers/Admin/LeadController
- Controllers/Admin/LeadActivityController
- Requests/Admin/_Lead_
- Services/CRM/LeadConversionService
- Notifications/Lead\*
- Exports/LeadsExport
- Console/Commands/SendLeadReminders

Frontend:

- pages/admin/leads
- components/m01

## M03

Backend:

- Models/Requirement\*
- Models/Estimate\*
- Controllers/Admin/Requirement\*
- Controllers/Admin/EstimateController
- Requests/Admin/_Requirement_
- Requests/Admin/StoreEstimateRequest
- Services/Requirements
- Services/Estimation
- Notifications/Requirement\*
- Exports/EstimateExport

Frontend:

- pages/admin/requirements
- pages/admin/estimates
- components/m03

## M04

Backend:

- Models/Proposal
- Models/Quotation
- Models/Contract
- Models/ContractDocument
- Controllers/Admin/ProposalController
- Controllers/Admin/QuotationController
- Controllers/Admin/ContractController
- Controllers/Admin/ContractDocumentController
- Services/Commercial
- Notifications/Commercial\*
- Exports/QuotationExport

Frontend:

- pages/admin/proposals
- pages/admin/quotations
- pages/admin/contracts
- components/m04
