# Keystone Architecture Decision Record (AI-Optimized)

## 1. System Overview
Keystone is a legally-secure property management platform. It facilitates connections between Landlords and Tenants through verified identities, automated compliance checks, and real-time offer negotiation.

## 2. Core Tech Stack
- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Svelte 5 (Runes) + Tailwind CSS v4
- **Database:** PostgreSQL 16+
- **Tooling:** Laravel Sail (Docker), Laravel Pint (Styling), Vitest (JS Testing), PHPUnit (PHP Testing)
- **UI System:** CondoClear Design System V3.0 (Manrope, Material Symbols, 0px border-radius)

## 3. Backend Architecture: Domain-Driven Design (DDD)
The backend is organized into bounded contexts within `app/Domain/`. Each domain contains its own Models, Actions, and State Machines.

### Domains & Bounded Contexts:
- **Identity:** Authentication, Role management (Landlord, Tenant, Guest).
- **Property:** Listing management, portfolios, and availability tracking.
- **Scheduling:** Visit request lifecycle and scheduling logic.
- **Negotiation:** Real-time offer submission, counter-offers, and verification.
- **Legal:** Lease generation, e-signatures, and polymorphic document management.

### Patterns:
- **Service Actions:** Business logic is encapsulated in single-purpose classes in `Domain/{Name}/Actions/`. 
- **Thin Controllers:** Controllers (`app/Http/Controllers/`) serve only as entry points that delegate to Actions.
- **State Pattern:** Complex workflows are managed via explicit state machines in `Domain/{Name}/States/`.
- **Persistent Verification:** Users have an `identity_document_id` for reusable verification across the platform.
- **Polymorphic Storage:** Legal documents are stored using a polymorphic strategy to support multiple entity types (Leases, Properties, Identities).

## 4. Workflow State Machines
Keystone utilizes the State pattern to ensure strict transitions across business processes:

- **Offer Status (Negotiation):**
  `Pending` -> `Countered` -> `Accepted` -> `AwaitingDocuments` -> `PendingVerification` -> `Verified` (or `Rejected`).
- **Lease Status (Legal):**
  `Draft` -> `WaitingTenantSignature` -> `WaitingLandlordSignature` -> `Active`.
- **Property Status (Property):**
  `Available` -> `Rented` -> `Maintenance`.
- **Visit Status (Scheduling):**
  `Pending` -> `Scheduled` -> `Rejected`.

## 5. Frontend Architecture: Component-Based Orchestration
The frontend uses Svelte 5's **Runes** for high-performance reactivity and a modular view system.

### Patterns:
- **Orchestrator Pattern:** `App.svelte` handles routing, global state (Auth), and data orchestration.
- **Modular Views:** Feature-specific UI blocks are extracted into `components/features/{View}View.svelte`.
- **Headless UI:** Uses `bits-ui` for accessible, unstyled components (Modals, Popovers).
- **Utility Styling:** Uses a `cn()` helper (clsx + tailwind-merge) for predictable class merging.

## 6. Database Strategy
- **Integrity:** Strict data types (e.g., `decimal` for rent/price) and cascade deletes for core relationships.
- **Compliance:** `offers` table includes a `compliance_status` column to gate lease generation.
- **Identities:** `users` table is linked to `documents` via `identity_document_id` for KYC-grade security.

## 7. Testing Mandates
- **Backend:** Feature tests in `tests/Feature/` must cover all Action outcomes and State transitions.
- **Frontend:** Unit tests in `resources/js/tests/` using Vitest for component rendering and state logic.
- **Validation:** Every commit is gated by a pre-commit hook running `sail pint --dirty`.

## 8. Design System (CondoClear V3.0)
- **Primary:** `#101022` (Deep Navy) | **Action:** `#1034F2` (Action Blue) | **Background:** `#F0EEE9` (Cloud Dancer)
- **Aesthetic:** High density, 0px border-radius, Manrope typography.
- **Interactions:** CSRF tokens are synced via `updateCsrfToken()` after session changes.
