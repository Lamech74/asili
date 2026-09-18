# Architecture Decisions

## ADR-001: Separate Vue and Laravel Applications

**Decision:** Keep the frontend and backend in separate `frontend/` and `backend/` directories.

**Reason:** This preserves a clean API boundary, supports future mobile clients, and lets the public site and admin CMS evolve independently.

**Alternatives considered:** A single Laravel-rendered application or a monolithic Vue application with embedded persistence.

**Consequences:** Local development requires two processes and explicit CORS/authentication configuration, but deployment and client evolution are more flexible.

## ADR-002: Version the API from the Start

**Decision:** Place REST endpoints under `/api/v1`.

**Reason:** The platform is intended to support future mobile applications and commerce workflows without breaking existing clients.

**Alternatives considered:** Unversioned endpoints or GraphQL.

**Consequences:** Response and route changes require deliberate versioning, while clients gain a stable contract.

## ADR-003: Delay Backend Scaffolding Until PHP Is Available

**Decision:** Establish documentation and the verifiable frontend foundation first; do not create a fake Laravel runtime.

**Reason:** The audit found no PHP executable. A backend generated without a runnable toolchain cannot be tested honestly.

**Alternatives considered:** Hand-writing a partial Laravel directory tree or switching to another backend.

**Consequences:** Phase 1 backend work is pending environment setup. The intended Laravel boundary and API contract are documented so implementation can resume cleanly.
