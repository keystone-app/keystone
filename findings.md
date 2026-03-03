# Findings & Scratchpad

## Frontend Architecture
- **Framework**: Svelte 5 (Vite-powered)
- **Styling**: Tailwind CSS 4.0
- **Entry Point**: `resources/js/app.js` -> `resources/js/App.svelte`
- **Views**:
    - **Landlord**: Property portfolio management.
    - **Tenant**: Lease tracking and document management.
    - **Common**: Legal-grade UI components with interactive feedback.

## API Integration Status
- Requested `GET /api/properties`, `GET /api/leases`, and `POST /api/documents/upload` from Junior.
- Currently using mock data for UI prototyping.

## Database Architecture (Prometheus)
- **Engine**: PostgreSQL 16+
- **Key Entities**:
    - `users`: Added `role` column (landlord, tenant, admin).
    - `properties`: Owned by landlords, tracks location and availability.
    - `leases`: Connects tenants to properties with term and rent details.
    - `documents`: Polymorphic storage for legal compliance metadata (leases, properties, identities).
- **Constraints**:
    - Cascade deletes on user/property relationships.
    - Strict data types for financial values (`decimal` for rent).