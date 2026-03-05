# Specification: End-to-End Maintenance Request Flow

## Overview
This track focuses on building out the complete Maintenance Request flow, utilizing the previously scaffolded `MaintenanceRequest` domain model and state machine. It will provide a seamless experience for tenants to report issues and for landlords to track and manage the resolution of these issues.

## Functional Requirements
- **Tenant Experience:**
    - Tenants can submit new maintenance requests from their dashboard, including a title and description.
    - Tenants can view a list of their submitted requests and their current status (`Reported`, `In Progress`, `Resolved`).
- **Landlord Experience:**
    - Landlords can view all maintenance requests across their properties.
    - Landlords can update the status of a request (e.g., from `Reported` to `In Progress` to `Resolved`).
- **Core Logic & State Machines:**
    - Implement the controller and domain actions (`SubmitMaintenanceRequestAction`, `UpdateMaintenanceStatusAction`) to handle the business logic.
    - Enforce valid state transitions within the `MaintenanceRequest` state machine.

## Non-Functional Requirements
- **UI/UX:** Adhere to existing mobile-first principles and product guidelines for all new dashboard components.
- **Testing:** Ensure 100% test coverage for the new controllers, actions, and frontend components.

## Acceptance Criteria
- [ ] Tenant can successfully submit a maintenance request via the UI.
- [ ] Landlord can view the request and update its status to `In Progress`.
- [ ] Tenant can see the status update reflected in their dashboard.
- [ ] Landlord can mark the request as `Resolved`.
- [ ] All state transitions are protected by domain logic and tests pass.

## Out of Scope
- Integration with external contractor dispatch APIs.
- Photo/Video uploads for maintenance requests (can be a future track).
