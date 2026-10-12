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
