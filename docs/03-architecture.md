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
