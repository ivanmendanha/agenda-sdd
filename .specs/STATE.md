# STATE

## Decisions

### AD-001
- **Decision**: The product has no authentication (no login, Sanctum, tokens, or auth middleware).
- **Reason**: Academic presentation only; user stated the project will not have authentication.
- **Trade-off**: No multi-user isolation or protected routes; all API endpoints are public.
- **Scope**: Entire application (API and React SPA)
- **Date**: 2026-11-04
- **Status**: active

### AD-002
- **Decision**: React runs inside Laravel at `resources/js` via the existing Vite pipeline; Laravel serves SPA and API on the same origin.
- **Reason**: User chose option 1.A; reuses HubDev/Vite ports already configured.
- **Trade-off**: No separate `frontend/` package or independent deployable SPA.
- **Scope**: Frontend bundling and routing
- **Date**: 2026-11-04
- **Status**: active

### AD-003
- **Decision**: The room-booking mockup UI is deferred; this scaffold ships only a React shell wired to a public API.
- **Reason**: User chose structure-only (2.A).
- **Trade-off**: First viewport will not match `.specs/ui/index.html` until a later feature.
- **Scope**: `react-api-structure` feature; future UI features consume the mockup as binding
- **Date**: 2026-11-04
- **Status**: active

## Handoff

