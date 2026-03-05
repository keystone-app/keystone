# Specification: 100% Frontend Test Coverage

## Overview
This track aims to achieve 100% test coverage for the frontend Svelte components and utilities. Currently, the coverage is approximately 55.4%. The primary focus will be on implementing missing tests for complex modals, tables, and the main `App.svelte` state orchestration.

## Target Areas for Coverage
- **Core Orchestration:**
    - `App.svelte`: Test global state transitions, authentication flows, and data fetching triggers.
- **Authentication Modals:**
    - `LoginModal.svelte` and `RegisterModal.svelte`: Cover form interactions, validation errors, and submission.
- **Data Displays (Tables & Lists):**
    - `OfferTable.svelte` and `VisitTable.svelte`: Test rendering of data rows and action button callbacks.
    - `Sidebar.svelte`: Test navigation triggers and role-based visibility.
- **Feature Modals:**
    - `OfferModal.svelte`, `PropertyModal.svelte`, and `VisitModal.svelte`: Improve coverage for multi-step forms and file uploads.
- **Dashboards:**
    - `TenantDashboard.svelte` and `LandlordDashboard.svelte`: Cover all view switches and data integrations.
- **Utilities:**
    - `utils.js`: Ensure `debounce` and other helpers are fully tested.

## Non-Functional Requirements
- **Consistency:** Use Vitest and `@testing-library/svelte`.
- **Reliability:** Tests should mock all `fetch` calls and global window/document objects as needed.
- **Maintainability:** Tests should be modular and follow existing patterns in `resources/js/tests/`.

## Acceptance Criteria
- [ ] `npx vitest run --coverage` reports 100% (or very close, accounting for Svelte 5 boilerplate) for all files in `resources/js/`.
- [ ] All existing and new tests pass successfully.

## Out of Scope
- Coverage for `node_modules`.
- E2E testing with Playwright/Cypress (this track is unit/component level).
