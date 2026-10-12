# Routes

## M00

| Method | URI                      | Permission              |
| ------ | ------------------------ | ----------------------- |
| GET    | /admin/users             | users.view              |
| POST   | /admin/users             | users.create            |
| GET    | /admin/users/{user}/edit | users.update            |
| PUT    | /admin/users/{user}      | users.update            |
| DELETE | /admin/users/{user}      | users.delete            |
| GET    | /admin/users/export      | users.export            |
| GET    | /admin/roles             | roles.manage            |
| POST   | /admin/roles             | roles.manage            |
| PUT    | /admin/roles/{role}      | roles.manage            |
| DELETE | /admin/roles/{role}      | roles.manage            |
| GET    | /admin/company           | company-settings.view   |
| PUT    | /admin/company           | company-settings.update |
| GET    | /admin/audit             | audit.view              |
| GET    | /admin/audit/excel       | audit.export            |
| GET    | /admin/audit/pdf         | audit.export            |
| GET    | /notifications           | authenticated user      |

## M01

| Method | URI                              | Permission    |
| ------ | -------------------------------- | ------------- |
| GET    | /admin/leads                     | leads.view    |
| GET    | /admin/leads/create              | leads.create  |
| POST   | /admin/leads                     | leads.create  |
| GET    | /admin/leads/{lead}              | leads.view    |
| GET    | /admin/leads/{lead}/edit         | leads.update  |
| PUT    | /admin/leads/{lead}              | leads.update  |
| PATCH  | /admin/leads/{lead}/stage        | leads.update  |
| DELETE | /admin/leads/{lead}              | leads.delete  |
| POST   | /admin/leads/{lead}/activities   | leads.update  |
| POST   | /admin/leads/{lead}/convert      | leads.convert |
| GET    | /admin/leads/export/excel        | leads.export  |
| GET    | /admin/leads/export/pipeline-pdf | leads.export  |

## M02 – Clients

| Method | URI                                                       | Permission                 |
| ------ | --------------------------------------------------------- | -------------------------- |
| GET    | /admin/clients/export/excel                               | clients.export             |
| GET    | /admin/clients                                            | clients.view               |
| GET    | /admin/clients/create                                     | clients.create             |
| POST   | /admin/clients                                            | clients.create             |
| GET    | /admin/clients/{client}                                   | clients.view               |
| GET    | /admin/clients/{client}/edit                              | clients.update             |
| PUT    | /admin/clients/{client}                                   | clients.update             |
| DELETE | /admin/clients/{client}                                   | clients.delete             |
| GET    | /admin/clients/{client}/profile-pdf                       | clients.view               |
| POST   | /admin/clients/{client}/contacts                          | clients.contacts.manage    |
| PUT    | /admin/clients/{client}/contacts/{contact}                | clients.contacts.manage    |
| DELETE | /admin/clients/{client}/contacts/{contact}                | clients.contacts.manage    |
| POST   | /admin/clients/{client}/billing-profiles                  | clients.billing.manage     |
| PUT    | /admin/clients/{client}/billing-profiles/{billingProfile} | clients.billing.manage     |
| DELETE | /admin/clients/{client}/billing-profiles/{billingProfile} | clients.billing.manage     |
| POST   | /admin/clients/{client}/documents                         | clients.documents.manage   |
| GET    | /admin/clients/{client}/documents/{document}/download     | clients.documents.download |
| DELETE | /admin/clients/{client}/documents/{document}              | clients.documents.manage   |
