# Implementation Plan: 100% Frontend Test Coverage

## Phase 1: Basic Components and Utilities
- [~] **Task: Complete coverage for `utils.js`**
  - [ ] Test `cn` and `debounce` helpers.
- [ ] **Task: Increase coverage for UI components**
  - [ ] Target remaining red lines in `MediaGallery`, `Modal`, `Select`, and `PriceDisplay`.

## Phase 2: Forms and Feature Modals
- [~] **Task: Implement tests for Authentication Modals**
  - [ ] `LoginModal.svelte`: Form submission and errors.
  - [ ] `RegisterModal.svelte`: Form submission and role selection.
- [ ] **Task: Increase coverage for Feature Modals**
  - [ ] `OfferModal.svelte`: Amount and terms input.
  - [ ] `PropertyModal.svelte`: Complex property form including media.
  - [ ] `VisitModal.svelte`: Scheduling flow.

## Phase 3: Tables, Dashboards, and Sidebar
- [ ] **Task: Implement tests for Table components**
  - [ ] `OfferTable.svelte`: Render offers and handle actions.
  - [ ] `VisitTable.svelte`: Render visits and handle actions.
- [ ] **Task: Increase coverage for Dashboards**
  - [ ] `TenantDashboard.svelte`: Cover all sub-views.
  - [ ] `LandlordDashboard.svelte`: Cover all sub-views.
- [ ] **Task: Implement tests for `Sidebar.svelte`**
  - [ ] Navigation and role-based link visibility.

## Phase 4: Main Application Orchestration
- [ ] **Task: Increase coverage for `App.svelte`**
  - [ ] Test authentication status checks.
  - [ ] Test data fetching triggers on view changes.
  - [ ] Test global action handlers (submitOffer, storeProperty, etc.).

## Phase 5: Final Validation
- [ ] **Task: Final Coverage Audit**
  - [ ] Run `npx vitest run --coverage`.
  - [ ] Address any remaining uncovered lines.
