# STATE

## Decisions

### AD-001
- **Decision**: The product has no authentication (no login, Sanctum, tokens, or auth middleware).
- **Reason**: Academic presentation only; user stated the project will not have authentication.
- **Trade-off**: No multi-user isolation or protected routes; all API endpoints are public.
- **Scope**: Entire application (API and React SPA)
- **Date**: 2026-11-04
- **Status**: AD-001 active

### AD-002
- **Decision**: React runs inside Laravel at `resources/js` via the existing Vite pipeline; Laravel serves SPA and API on the same origin.
- **Reason**: User chose option 1.A; reuses HubDev/Vite ports already configured.
- **Trade-off**: No separate `frontend/` package or independent deployable SPA.
- **Scope**: Frontend bundling and routing
- **Date**: 2026-11-04
- **Status**: AD-002 active

### AD-003
- **Decision**: The room-booking mockup UI is deferred; this scaffold ships only a React shell wired to a public API.
- **Reason**: User chose structure-only (2.A).
- **Trade-off**: First viewport will not match `.specs/ui/index.html` until a later feature.
- **Scope**: `react-api-structure` feature; future UI features consume the mockup as binding
- **Date**: 2026-11-04
- **Status**: AD-003 superseded by AD-005

### AD-004
- **Decision**: The React SPA is written in TypeScript (`.ts` / `.tsx`) with strict `tsconfig.json`.
- **Reason**: User requested replacing the JS implementation with TypeScript.
- **Trade-off**: Requires `typescript` and `@types/react*` as devDependencies; no plain `.jsx` sources under `resources/js`.
- **Scope**: Frontend under `resources/js`
- **Date**: 2026-11-04
- **Status**: AD-004 active

### AD-005
- **Decision**: The `app-layout` feature ships mockup chrome (topbar, nav tabs, content container, design tokens) but not the mockup domain body (dashboard cards, rooms grid, reservations list, modals). Route bodies are minimal placeholders inside the shared outlet.
- **Reason**: User chose options 1.A, 2.A, 4.A, and 5.A when specifying the common layout; question: include mockup body content now? option chosen: outlet/placeholder only.
- **Trade-off**: First screens match the mockup frame, not the full mockup pages; domain UI remains later features.
- **Scope**: `app-layout` feature; supersedes AD-003 for shared chrome only
- **Date**: 2026-11-04
- **Status**: AD-005 active

## Handoff

- **Feature**: app-layout
- **Phase**: Done — validate_verification green
- **Completed**: T1–T7, walk, screens matches, verification findings closed
- **Next**: Domain page features (Dashboard / Salas / Minhas Reservas) against the shared chrome
- **Branch**: master
