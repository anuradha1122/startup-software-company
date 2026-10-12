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
