# Architecture

## Backend

Laravel modular domain-oriented application.

Business workflows should be placed in service/action classes rather
than large controllers.

## Frontend

React + TypeScript + Inertia.

## Authorization

Laravel policies and Spatie permissions.

## IDs

Internal database keys may use bigint IDs.

Public-facing application references use ULIDs where exposed.

## Background Processing

Queues are mandatory for notifications, mail and future document jobs.

Local/default driver: database.

Production target: Redis/Horizon when infrastructure is available.

## Files

Sensitive documents use private Laravel storage.

Only explicitly public assets such as company branding may use the
public disk.

## M05 Architecture

Commercial baseline:

Signed Contract
→ Project

Project references remain linked to:

- Contract
- Client
- Approved Requirement Version
- Approved Estimate

Project operational data is separated into:

- ProjectCharter
- ProjectPhase
- ProjectMilestone
- ProjectStakeholder
- ProjectRisk
- ProjectDecision

## M06 Architecture

Project execution hierarchy:

Project
→ Phase / Milestone
→ Task
→ Workflow Transition
→ Work Submission
→ Review

Task workflow status is normalized through task_workflow_statuses
instead of hard-coded task status strings.

Work submission versions preserve review history.

## M07 Architecture

User
→ ResourceProfile
→ Skills

Project
→ ProjectAssignment
→ User

M06 task ownership is validated through ProjectAssignment.

ProjectAssignment is the operational project membership boundary.

M15 remains responsible for HR employment, attendance, leave and
payroll.

## M08 Architecture

User
→ ResourceCostRate

User
→ Timesheet
→ TimesheetEntry
→ ProjectAssignment
→ ProjectTask

Only approved timesheet entries contribute to internal project cost.

Cost rate values are snapshotted onto approved entries to preserve
historical profitability calculations.
