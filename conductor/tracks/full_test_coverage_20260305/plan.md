# Implementation Plan: 100% Backend Test Coverage

## Phase 1: Authentication and Identity (Big Gaps) [checkpoint: c3fab17]
- [x] **Task: Implement tests for `AuthController`** c3fab17
  - [x] Test `/login` (success and failure). c3fab17
  - [x] Test `/register` (success and validation failure). c3fab17
  - [x] Test `/logout`. c3fab17
  - [x] Test `/me` endpoint for authenticated/unauthenticated users. c3fab17
- [x] **Task: Implement tests for `RegisterGuestAction`** c3fab17
  - [x] Unit tests for user creation and role assignment. c3fab17
- [x] **Task: Implement tests for `UploadIdentityDocumentAction`** c3fab17
  - [x] Unit tests for document storage and relationship linking. c3fab17

## Phase 2: Documents and Leases
- [ ] **Task: Implement tests for `DocumentController`**
  - [ ] Test compliance document uploads for offers.
  - [ ] Test identity document uploads.
- [ ] **Task: Increase coverage for `LeaseController`**
  - [ ] Test `uploadDocument` route.
  - [ ] Test `sign` route including edge cases.
- [ ] **Task: Complete unit tests for `Document` model**
  - [ ] Test polymorphic relationships (`documentable`).
  - [ ] Test scopes and custom methods.

## Phase 3: Models and Controllers Refinement
- [ ] **Task: Complete coverage for `User`, `Property`, and `Offer` models**
  - [ ] Test all remaining relationships and helper methods.
- [ ] **Task: Increase coverage for `VisitController` and `MaintenanceController`**
  - [ ] Cover all remaining error paths (unauthorized, invalid transitions).
- [ ] **Task: Increase coverage for Domain Actions**
  - [ ] Target remaining red lines in `SignLeaseAction`, `UploadComplianceDocumentAction`, etc.

## Phase 4: Final Validation
- [ ] **Task: Final Coverage Audit**
  - [ ] Run `./vendor/bin/sail artisan test --coverage`.
  - [ ] Identify and fix any remaining "pockets" of missing coverage.
- [ ] **Task: Conductor - User Manual Verification (Protocol in workflow.md)**
  - [ ] Ensure all tests provide meaningful validation beyond just "hitting the line".
