# Implementation Plan: End-to-End Maintenance Request Flow

## Phase 1: Maintenance Request Domain Actions [checkpoint: 24be417]
- [x] **Task: Write tests for SubmitMaintenanceRequestAction** 56204cc
  - [x] Implement unit tests ensuring a tenant can submit a request for their active lease. 56204cc
- [x] **Task: Implement `SubmitMaintenanceRequestAction`** 56204cc
  - [x] Create the domain action logic. 56204cc
- [x] **Task: Write tests for UpdateMaintenanceStatusAction** 020e9fe
  - [x] Implement unit tests ensuring a landlord can update the status of a request for their property. 020e9fe
- [x] **Task: Implement `UpdateMaintenanceStatusAction`** 020e9fe
  - [x] Create the domain action logic enforcing state transitions. 020e9fe
- [x] **Task: Conductor - User Manual Verification 'Phase 1: Maintenance Request Domain Actions' (Protocol in workflow.md)** 24be417

## Phase 2: API Endpoints (Controllers) [checkpoint: 00f4b94]
- [x] **Task: Write tests for `MaintenanceController` endpoints** 24d109d
  - [x] Implement feature tests for `index` (list), `store` (create), and `update` (change status) routes. 24d109d
- [x] **Task: Implement `MaintenanceController`** 24d109d
  - [x] Create the controller and wire it to the domain actions. 24d109d
- [x] **Task: Define API Routes** 24d109d
  - [x] Add the new routes to `routes/web.php` under the `auth` middleware. 24d109d
- [x] **Task: Conductor - User Manual Verification 'Phase 2: API Endpoints (Controllers)' (Protocol in workflow.md)** 00f4b94

## Phase 3: Tenant UI Implementation [checkpoint: d46d6e8]
- [x] **Task: Write tests for `MaintenanceRequestModal`** 1d31469
  - [x] Ensure Vitest coverage for the form submission. 1d31469
- [x] **Task: Create `MaintenanceRequestModal` component** 1d31469
  - [x] Implement the UI for tenants to submit a new request. 1d31469
- [x] **Task: Update `TenantDashboard`** 1d31469
  - [x] Integrate the modal and display a table/list of submitted requests. 1d31469
- [x] **Task: Conductor - User Manual Verification 'Phase 3: Tenant UI Implementation' (Protocol in workflow.md)** d46d6e8

## Phase 4: Landlord UI Implementation [checkpoint: d46d6e8]
- [x] **Task: Write tests for Landlord Maintenance View** 1d31469
  - [x] Ensure Vitest coverage for viewing and updating requests. 1d31469
- [x] **Task: Update `LandlordDashboard`** 1d31469
  - [x] Add a new section/table to display maintenance requests for their properties. 1d31469
- [x] **Task: Implement Status Update UI** 1d31469
  - [x] Add controls for the landlord to transition request states. 1d31469
- [x] **Task: Conductor - User Manual Verification 'Phase 4: Landlord UI Implementation' (Protocol in workflow.md)** d46d6e8
