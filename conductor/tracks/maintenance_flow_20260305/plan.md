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

## Phase 2: API Endpoints (Controllers)
- [ ] **Task: Write tests for `MaintenanceController` endpoints**
  - [ ] Implement feature tests for `index` (list), `store` (create), and `update` (change status) routes.
- [ ] **Task: Implement `MaintenanceController`**
  - [ ] Create the controller and wire it to the domain actions.
- [ ] **Task: Define API Routes**
  - [ ] Add the new routes to `routes/web.php` under the `auth` middleware.
- [ ] **Task: Conductor - User Manual Verification 'Phase 2: API Endpoints (Controllers)' (Protocol in workflow.md)**

## Phase 3: Tenant UI Implementation
- [ ] **Task: Write tests for `MaintenanceRequestModal`**
  - [ ] Ensure Vitest coverage for the form submission.
- [ ] **Task: Create `MaintenanceRequestModal` component**
  - [ ] Implement the UI for tenants to submit a new request.
- [ ] **Task: Update `TenantDashboard`**
  - [ ] Integrate the modal and display a table/list of submitted requests.
- [ ] **Task: Conductor - User Manual Verification 'Phase 3: Tenant UI Implementation' (Protocol in workflow.md)**

## Phase 4: Landlord UI Implementation
- [ ] **Task: Write tests for Landlord Maintenance View**
  - [ ] Ensure Vitest coverage for viewing and updating requests.
- [ ] **Task: Update `LandlordDashboard`**
  - [ ] Add a new section/table to display maintenance requests for their properties.
- [ ] **Task: Implement Status Update UI**
  - [ ] Add controls for the landlord to transition request states.
- [ ] **Task: Conductor - User Manual Verification 'Phase 4: Landlord UI Implementation' (Protocol in workflow.md)**
