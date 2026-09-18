# Asili Naturals Architecture

## Phase 0 Assessment

The workspace was empty at audit time. No existing `package.json`, `composer.json`, Laravel application, Vue application, routes, migrations, environment files, or reusable components were present. The local shell provides Node.js 14.21.3, npm 6.14.18, and Composer 2.8.12. PHP and the MySQL client are not currently available on `PATH`, so Laravel installation and database verification must wait for the PHP/XAMPP CLI environment to be exposed.

## Recommended Architecture

Use a separated monorepo-style structure:

- `frontend/`: Vue 3 + TypeScript + Vite single-page application.
- `backend/`: Laravel API, versioned under `/api/v1`.
- `docs/`: architecture, database, API, security, deployment, and decision records.

The frontend owns presentation, routing, local UI state, and typed API modules. The backend owns authentication, authorization, validation, persistence, media handling, audit logging, and business rules. The browser must never be treated as the security boundary.

## Technology Decisions

- Vue 3 Composition API with TypeScript.
- Vite for frontend development and production builds.
- Vue Router for public/admin route separation and lazy loading.
- Pinia only for genuinely shared state such as authentication, settings, and notifications.
- Axios behind a central API client and module services.
- Tailwind CSS plus project design tokens for consistent UI primitives.
- Laravel Sanctum for SPA authentication once PHP is available.
- MySQL as the initial relational database, with migrations and foreign keys.
- Pest or PHPUnit for backend tests and Vitest/Vue Test Utils for frontend tests.

## Database Overview

The current domain includes users, roles, permissions, categories, products, product images, services, blog content, testimonials, FAQs, contact messages, media, settings, and audit logs. Products, services, blog posts, and users should support soft deletion where appropriate. Large collections must be paginated and indexed for slugs, statuses, publication dates, and common search fields.

Future commerce tables such as customers, orders, payments, reviews, wishlists, coupons, inventory transactions, and deliveries should be introduced only when their workflows are implemented. Payment provider integration should use a gateway interface and never store credentials in source code.

## API Overview

Expose a consistent JSON envelope under `/api/v1`:

- Public read endpoints for products, categories, services, blog, FAQs, settings, and testimonials.
- Public contact submission endpoint with validation and rate limiting.
- Sanctum-protected authentication endpoints for login, logout, and current user.
- Policy-protected admin CRUD endpoints for content and settings.

Successful responses use `success`, `message`, and `data`. Validation responses use HTTP 422 with `success`, `message`, and `errors`.

## Frontend Architecture

Use page-level route components under `src/pages`, reusable UI under `src/components`, Pinia stores for shared state, composables for reusable behavior, and typed services under `src/services`. Pages must not contain raw Axios calls or database assumptions. Public and admin layouts should share accessible primitives but remain visually distinct enough for their workflows.

## Admin Architecture

The admin area will use an authenticated layout with capability-aware navigation. Authorization will be enforced by Laravel policies and gates; frontend permission checks only improve usability. Each CRUD surface will provide loading, empty, error, validation, confirmation, pagination, and success states. Audit logs will capture important administrative changes without recording passwords, tokens, or other secrets.

## Security Considerations

- Enforce authorization server-side for every protected operation.
- Validate all request payloads and uploaded files with Laravel Form Requests.
- Use hashed passwords, secure Sanctum session handling, CSRF protection, CORS configuration, and rate limits.
- Keep secrets in environment variables; never commit `.env` files.
- Avoid exposing stack traces or sensitive data in production responses.
- Store media through a filesystem abstraction so local storage can later be replaced by S3 or another object store.
- Log administrative actions, but redact credentials and secrets.

## Development Phases

1. Phase 0: audit and architecture documentation.
2. Phase 1: frontend/backend project foundation, design tokens, API client, and base routing.
3. Phase 2: database migrations, models, factories, and seeders.
4. Phase 3: Sanctum authentication, roles, permissions, and policies.
5. Phase 4: versioned public and admin APIs.
6. Phase 5: public website pages connected to real APIs.
7. Phase 6: admin CMS and media workflows.
8. Phase 7: UX, accessibility, responsive behavior, and error states.
9. Phase 8: SEO, structured data, sitemap, and robots architecture.
10. Phase 9: performance review and caching.
11. Phase 10: security review.
12. Phase 11: automated and manual testing.
13. Phase 12: deployment and production readiness.

## Current Implementation Status

PHP 8.3 and MySQL are available through XAMPP. The Laravel backend is configured for the `asili_naturals` database and currently includes the initial category/product/contact schema, public `/api/v1` endpoints, Sanctum bearer-token authentication, and server-side role protection for admin product access. The next backend increment is full admin CRUD with policies, audit logging, and content resources.
