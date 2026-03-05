# Implementation Plan: Tenant Compliance and Verification Flow

## Phase 1: Compliance Document Management [checkpoint: e9f1b54]
- [x] **Task: Write tests for `UploadComplianceDocumentAction` enhancement** (c6383e2)
- [x] **Task: Refine `UploadComplianceDocumentAction` implementation** (c6383e2)
- [x] **Task: Write tests for automated status transition on upload** (c6383e2)
- [x] **Task: Conductor - User Manual Verification 'Phase 1: Compliance Document Management' (Protocol in workflow.md)**

## Phase 2: Income Verification & Status Transitions
- [ ] **Task: Write tests for `VerifyIncomeAction` refinement**
  - [ ] Test verification success and failure scenarios for ` Brazil Open Finance` mock.
- [ ] **Task: Refine `VerifyIncomeAction` implementation**
  - [ ] Implement robust transition to `Verified` status.
- [ ] **Task: Write tests for final transition from Verified to Lease Draft creation**
  - [ ] Ensure `Verified` status triggers `CreateLeaseFromOfferAction`.
- [ ] **Task: Conductor - User Manual Verification 'Phase 2: Income Verification & Status Transitions' (Protocol in workflow.md)**

## Phase 3: Automated Lease Creation
- [ ] **Task: Write tests for `CreateLeaseFromOfferAction` automation**
  - [ ] Test that a `Lease` draft is correctly created with all data from the offer.
- [ ] **Task: Implement automated Lease Draft creation trigger**
  - [ ] Add event listener or direct action call when `Offer` status transitions to `Verified`.
- [ ] **Task: Conductor - User Manual Verification 'Phase 3: Automated Lease Creation' (Protocol in workflow.md)**

## Phase 4: UI/UX Enhancements
- [ ] **Task: Write tests for `TenantDashboard` UI updates**
  - [ ] Test showing correct document status (Pending/Uploaded/Verified).
- [ ] **Task: Update `TenantDashboard` frontend**
  - [ ] Add document upload progress and verification buttons.
- [ ] **Task: Add notifications for Offer status changes**
  - [ ] Implement flash messages or toast notifications in the UI.
- [ ] **Task: Conductor - User Manual Verification 'Phase 4: UI/UX Enhancements' (Protocol in workflow.md)**
