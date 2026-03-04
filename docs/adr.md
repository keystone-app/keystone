# Keystone Architecture Decision Record (AI-Optimized)

## 1. System Overview
Keystone is a legally-secure property management platform. It facilitates connections between Landlords and Tenants through verified identities, automated compliance checks, and real-time offer negotiation.

## 2. Core Tech Stack
- **Backend:** Laravel 11 (PHP 8.2+)
- **Frontend:** Svelte 5 (Runes) + Tailwind CSS v4
- **Database:** PostgreSQL
- **Tooling:** Laravel Sail (Docker), Laravel Pint (Styling), Vitest (JS Testing), PHPUnit (PHP Testing)
- **UI System:** CondoClear Design System V3.0 (Manrope, Material Symbols, 0px border-radius)

## 3. Backend Architecture: Domain-Driven Design (DDD)
The backend is organized into bounded contexts within `app/Domain/`. Each domain contains its own Models and Actions.

### Domains:
- **Identity:** Authentication, User management, Roles (landlord, tenant, guest).
- **Property:** Listings, portfolios, property features.
- **Scheduling:** Visit requests and status management.
- **Negotiation:** Offer submission, counter-offers, and status flow.
- **Legal:** Leases, compliance documents, and identity verification.

### Patterns:
- **Service Actions:** Business logic is encapsulated in single-purpose classes in `Domain/{Name}/Actions/`. 
- **Thin Controllers:** Controllers (`app/Http/Controllers/`) serve only as entry points that delegate to Actions.
- **Persistent Verification:** Users have an `identity_document_id` for reusable verification across the platform.

## 4. Frontend Architecture: Component-Based Orchestration
The frontend uses Svelte 5's **Runes** for high-performance reactivity.

### Patterns:
- **Orchestrator Pattern:** `App.svelte` acts as a clean router and data provider.
- **Modular Views:** Massive UI blocks are extracted into `components/features/{View}View.svelte` components.
- **Nested Navigation:** `Sidebar.svelte` handles role-based sub-menus and notification badges.
- **Headless UI:** Uses `bits-ui` for accessible, unstyled components (Modals, Popovers).
- **Utility Styling:** Uses a `cn()` helper (clsx + tailwind-merge) for predictable class merging.

## 5. Offer Workflow State Machine
Offers follow a strict progression:
`pending` -> `accepted` -> `awaiting_documents` -> `pending_verification` -> `verified`

## 6. Testing Mandates
- **Backend:** Feature tests in `tests/Feature/` must cover all Action outcomes.
- **Frontend:** Unit tests in `resources/js/tests/` must cover component rendering, filtering logic, and state transitions.
- **Validation:** Every commit is gated by a pre-commit hook running `sail pint --dirty`.

## 7. Design System (CondoClear V3.0)
- **Primary:** `#101022` (Deep Navy)
- **Action:** `#1034F2` (Action Blue)
- **Background:** `#F0EEE9` (Cloud Dancer)
- **Aesthetic:** Straight lines (`border-radius: 0px`), high density, professional typography (Manrope).

## 8. Integration Hooks
- **CSRF:** Sync via `updateCsrfToken()` meta-tag update after session changes.
- **Auth:** Global state managed in `App.svelte` and synced via `/me` endpoint.
