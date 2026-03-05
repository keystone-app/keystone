# Specification: Advanced Property Search UI

## Overview
This track focuses on enhancing the property discovery experience by implementing a comprehensive filtering UI in the `ListingsView`. Users will be able to filter properties based on price range, type, and availability, utilizing the existing server-side search logic.

## Functional Requirements
- **Filter Bar:**
    - Add a dedicated filtering section at the top of the `ListingsView`.
    - Support filtering by `Min Price` and `Max Price` (numeric inputs).
    - Support filtering by `Property Type` (dropdown: Apartment, House, Loft, Studio).
    - Support filtering by `Status` (dropdown: Available, Pending).
- **Search Logic:**
    - Real-time filtering: Updating a filter should automatically trigger a new fetch request to `/properties` with the relevant query parameters.
    - Clear Filters: Provide a button to reset all filters to their default states.
- **Loading States:**
    - Display a loading indicator while the filtered results are being fetched.

## Non-Functional Requirements
- **Mobile Responsiveness:** The filter bar must be optimized for mobile (e.g., using a collapsible drawer or horizontal scrolling on small screens).
- **Performance:** Ensure efficient re-renders and avoid redundant API calls (use debouncing for text/numeric inputs if necessary).
- **Design Consistency:** Use existing Bits UI and Tailwind CSS components to maintain the platform's professional aesthetic.

## Acceptance Criteria
- [ ] User can filter listings by a specific price range and see correct results.
- [ ] User can filter listings by property type (e.g., 'Apartment') and see only apartments.
- [ ] The UI remains responsive and intuitive on both desktop and mobile devices.
- [ ] All filter interactions are verified with Vitest component tests.

## Out of Scope
- Map-based search (future track).
- Saved searches or "search alerts".
- Multi-select for property types (start with single-select for simplicity).
