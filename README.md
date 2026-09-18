# Asili Naturals

A production-oriented wellness business platform for Asili Naturals, built as a Vue frontend and Laravel API.

## Status

Phase 0 architecture audit is complete. The Vue foundation and Laravel API foundation are now in place. The backend is connected to the local XAMPP MySQL instance and includes public product/contact endpoints, Sanctum authentication, and role-protected admin product access.

See [docs/architecture.md](docs/architecture.md) for the system assessment and phased plan.

## Planned Structure

- `frontend/` - Vue 3, TypeScript, Vite, Tailwind CSS, Vue Router, Pinia, and Axios.
- `backend/` - Laravel API with Sanctum, MySQL persistence, policies, resources, and tests.
- `docs/` - architecture and implementation records.

## Prerequisites

- Node.js 18+ and npm
- PHP CLI compatible with the selected Laravel release
- Composer
- MySQL

## Frontend Commands

```bash
cd frontend
npm install
npm run dev
npm run build
npm run typecheck
```

The current machine has Node 14, which can run the Vite bundle but cannot execute the Vue typecheck toolchain. Upgrade Node before relying on `npm run typecheck`.

## Backend Commands

```bash
cd backend
php artisan migrate
php artisan test
php artisan serve
```

The API is versioned under `/api/v1`. Authentication uses Sanctum bearer tokens. Users have a `customer` role by default; `admin` and `editor` roles can access protected admin content routes. Never commit real environment files or secrets.
