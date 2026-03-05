# Tech Stack: Keystone

## Core Platform
- **Backend Language:** PHP 8.2+
- **Backend Framework:** Laravel 12.0
- **Frontend Framework:** Svelte with TailwindCSS 4.0 and Vite
- **Database:** Standard SQL (supporting MySQL, PostgreSQL, SQLite)

## Key Libraries & Components
- **Domain Logic:** Spatie Model States (for complex state machines in Property, Legal, and Negotiation domains).
- **Frontend Components:** Bits UI, Lucide Svelte, tailwind-merge, clsx.
- **Infrastructure:** Laravel Envoy (for zero-downtime deployments).
- **Communication:** Axios (for HTTP requests in the frontend).

## Quality Assurance
- **Backend Testing:** PHPUnit, Laravel Pail, nunomaduro/collision.
- **Frontend Testing:** Vitest, @testing-library/svelte, @testing-library/jest-dom, @testing-library/user-event.
- **Static Analysis:** PHPStan, nunomaduro/larastan.
- **Code Style:** Laravel Pint.
- **Local Environment:** Laravel Sail.
