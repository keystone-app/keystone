# Specification: 100% Backend Test Coverage

## Overview
This track aims to achieve 100% test coverage for the backend PHP codebase (models, actions, and controllers). Currently, the coverage stands at approximately 71.7%. The primary focus will be on implementing missing tests for critical authentication and document handling logic, as well as covering edge cases and error paths in existing modules.

## Target Areas for Coverage
- **Controllers (0% or Low Coverage):**
    - `AuthController`: Complete missing tests for login, registration, logout, and identity checking.
    - `DocumentController`: Implement tests for compliance and identity document uploads.
    - `LeaseController`: Increase coverage for document uploading and signing.
    - `VisitController`: Cover missing error paths and authorization checks.
- **Actions (Missing or Incomplete):**
    - `RegisterGuestAction`: Implement full unit tests for user registration.
    - `UploadIdentityDocumentAction`: Implement full unit tests for identity document handling.
    - `SignLeaseAction`: Add tests for edge cases and authorization.
- **Models (Relationship and Scope Coverage):**
    - `User`: Test all remaining relationships and model methods.
    - `Document`: Test polymorphic relationships and scopes.
    - `Property`: Test remaining model logic.
- **Error Paths & Authorization:**
    - Systematically identify and test all `unauthorized`, `unauthenticated`, and `InvalidArgumentException` paths in controllers and actions.

## Non-Functional Requirements
- **Consistency:** Use existing PHPUnit and Laravel testing patterns (e.g., `RefreshDatabase`, `actingAs`).
- **Performance:** Ensure the test suite remains fast by using in-memory SQLite where appropriate.
- **Maintainability:** Tests should be clean, readable, and well-documented.

## Acceptance Criteria
- [ ] `./vendor/bin/sail artisan test --coverage` reports 100% (or as close as practically possible, accounting for Laravel boilerplate) for all files in `app/`.
- [ ] All existing and new tests pass successfully.

## Out of Scope
- Coverage for vendor files (Laravel framework itself).
- Coverage for frontend Svelte components (handled in separate tracks, though usually aimed at 100% as well).
- Database migration coverage.
