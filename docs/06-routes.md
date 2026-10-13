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

## M03 – Requirements & Estimates

| Method | URI                                                                                    | Permission                        |
| ------ | -------------------------------------------------------------------------------------- | --------------------------------- |
| GET    | /admin/requirements                                                                    | requirements.view                 |
| GET    | /admin/requirements/create                                                             | requirements.create               |
| POST   | /admin/requirements                                                                    | requirements.create               |
| GET    | /admin/requirements/{requirement}                                                      | requirements.view                 |
| GET    | /admin/requirements/{requirement}/versions/{version}/edit                              | requirements.update               |
| PUT    | /admin/requirements/{requirement}/versions/{version}                                   | requirements.update               |
| POST   | /admin/requirements/{requirement}/revisions                                            | requirements.update               |
| POST   | /admin/requirements/{requirement}/versions/{version}/review                            | requirements.review               |
| POST   | /admin/requirements/{requirement}/versions/{version}/approval-request                  | requirements.request-approval     |
| POST   | /admin/requirements/{requirement}/versions/{version}/approvals/{approval}/decision     | requirements.record-approval      |
| GET    | /admin/requirements/{requirement}/versions/{version}/srs-pdf                           | requirements.export               |
| POST   | /admin/requirements/{requirement}/versions/{version}/attachments                       | requirements.attachments.manage   |
| GET    | /admin/requirements/{requirement}/versions/{version}/attachments/{attachment}/download | requirements.attachments.download |
| DELETE | /admin/requirements/{requirement}/versions/{version}/attachments/{attachment}          | requirements.attachments.manage   |
| GET    | /admin/requirements/{requirement}/versions/{version}/estimates/create                  | estimates.create                  |
| POST   | /admin/requirements/{requirement}/versions/{version}/estimates                         | estimates.create                  |
| GET    | /admin/estimates/{estimate}/edit                                                       | estimates.view                    |
| PUT    | /admin/estimates/{estimate}                                                            | estimates.update                  |
| POST   | /admin/estimates/{estimate}/approve                                                    | estimates.approve                 |
| GET    | /admin/estimates/{estimate}/excel                                                      | estimates.export                  |
| GET    | /admin/estimates/{estimate}/pdf                                                        | estimates.export                  |

## M04 – Proposals, Quotations & Contracts

### Proposals

| Method | URI                                  | Permission                |
| ------ | ------------------------------------ | ------------------------- |
| GET    | /admin/proposals                     | proposals.view            |
| GET    | /admin/proposals/create              | proposals.create          |
| POST   | /admin/proposals                     | proposals.create          |
| GET    | /admin/proposals/{proposal}          | proposals.view            |
| PUT    | /admin/proposals/{proposal}          | proposals.update          |
| POST   | /admin/proposals/{proposal}/approve  | proposals.approve         |
| POST   | /admin/proposals/{proposal}/send     | proposals.send            |
| POST   | /admin/proposals/{proposal}/decision | proposals.record-decision |
| GET    | /admin/proposals/{proposal}/pdf      | proposals.export          |

### Quotations

| Method | URI                                           | Permission                 |
| ------ | --------------------------------------------- | -------------------------- |
| GET    | /admin/quotations                             | quotations.view            |
| GET    | /admin/quotations/create                      | quotations.create          |
| POST   | /admin/quotations                             | quotations.create          |
| GET    | /admin/quotations/{quotation}                 | quotations.view            |
| PUT    | /admin/quotations/{quotation}                 | quotations.update          |
| POST   | /admin/quotations/{quotation}/approve         | quotations.approve         |
| POST   | /admin/quotations/{quotation}/send            | quotations.send            |
| POST   | /admin/quotations/{quotation}/decision        | quotations.record-decision |
| GET    | /admin/quotations/{quotation}/excel           | quotations.export          |
| GET    | /admin/quotations/{quotation}/pdf             | quotations.export          |
| GET    | /admin/quotations/{quotation}/contract/create | contracts.create           |

### Contracts

| Method | URI                                                       | Permission                   |
| ------ | --------------------------------------------------------- | ---------------------------- |
| GET    | /admin/contracts                                          | contracts.view               |
| POST   | /admin/contracts                                          | contracts.create             |
| GET    | /admin/contracts/{contract}                               | contracts.view               |
| POST   | /admin/contracts/{contract}/issue                         | contracts.issue              |
| POST   | /admin/contracts/{contract}/signed                        | contracts.mark-signed        |
| GET    | /admin/contracts/{contract}/pdf                           | contracts.export             |
| POST   | /admin/contracts/{contract}/documents                     | contracts.documents.manage   |
| GET    | /admin/contracts/{contract}/documents/{document}/download | contracts.documents.download |

**## M05 – Projects**

| Method | URI                                                       | Permission                   |
| ------ | --------------------------------------------------------- | ---------------------------- |
| GET    | /admin/projects/export/excel                              | projects.export              |
| GET    | /admin/projects                                           | projects.view                |
| GET    | /admin/projects/create                                    | projects.create              |
| POST   | /admin/projects                                           | projects.create              |
| GET    | /admin/projects/{project}                                 | projects.view                |
| PUT    | /admin/projects/{project}                                 | projects.update              |
| POST   | /admin/projects/{project}/activate                        | projects.activate            |
| POST   | /admin/projects/{project}/hold                            | projects.update              |
| POST   | /admin/projects/{project}/resume                          | projects.update              |
| POST   | /admin/projects/{project}/complete                        | projects.complete            |
| POST   | /admin/projects/{project}/kickoff                         | projects.kickoff.send        |
| GET    | /admin/projects/{project}/charter-pdf                     | projects.export              |
| GET    | /admin/projects/{project}/charters/{charter}/edit         | projects.charter.manage      |
| PUT    | /admin/projects/{project}/charters/{charter}              | projects.charter.manage      |
| POST   | /admin/projects/{project}/charters/{charter}/approve      | projects.charter.approve     |
| POST   | /admin/projects/{project}/charters/revision               | projects.charter.manage      |
| POST   | /admin/projects/{project}/phases                          | projects.phases.manage       |
| DELETE | /admin/projects/{project}/phases/{phase}                  | projects.phases.manage       |
| POST   | /admin/projects/{project}/milestones                      | projects.milestones.manage   |
| POST   | /admin/projects/{project}/milestones/{milestone}/complete | projects.milestones.manage   |
| POST   | /admin/projects/{project}/stakeholders                    | projects.stakeholders.manage |
| POST   | /admin/projects/{project}/risks                           | projects.risks.manage        |
| POST   | /admin/projects/{project}/risks/{risk}/escalate           | projects.risks.escalate      |
| POST   | /admin/projects/{project}/risks/{risk}/close              | projects.risks.manage        |
| POST   | /admin/projects/{project}/decisions                       | projects.decisions.manage    |

## M06 – Project Tasks & Work Submissions

### Project Tasks

Method URI Permission
GET /admin/projects//tasks/board tasks.view
POST /admin/projects//tasks tasks.create
GET /admin/projects//tasks/export/excel tasks.export
GET /admin/projects//tasks/export/pdf tasks.export
GET /admin/tasks/ tasks.view
PUT /admin/tasks/ tasks.update
DELETE /admin/tasks/ tasks.delete
POST /admin/tasks//move tasks.move
POST /admin/tasks//comments tasks.comment
POST /admin/tasks//submissions work-submissions.create

### Work Submissions

Method URI Permission
POST /admin/work-submissions//review work-submissions.review
GET /admin/work-submissions//attachments//download work-submissions.attachments.download
