# React API Structure Specification

**Approved**: 2026-11-04 - user: "aprovado"

## Problem Statement

The academic room-booking demo needs a clear split between a Laravel JSON API and a React SPA, but the repo is still a default Laravel+Vite skeleton with an empty JS entry and no API routes. Without this scaffold, later features have nowhere consistent to put React code or public API endpoints.

## Goals

- [ ] A React app mounts from Vite inside `resources/js` when the developer opens the app root URL
- [ ] Public JSON API routes are registered under `/api` and callable without authentication
- [ ] Frontend code has a documented folder layout and a shared HTTP helper aimed at `/api`

## Out of Scope

| Feature | Reason |
| ------- | ------ |
| Port of Dashboard / Salas / Minhas Reservas / modals from the mockup | User chose structure only (2.A); UI is a later feature |
| Domain models, migrations, and reservation business rules | Scaffolding only |
| Authentication (login, Sanctum, tokens, guards) | Academic presentation project — no auth for the whole product |
| Separate `frontend/` package or second Vite root | User chose integrated `resources/js` (1.A) |
| CORS for third-party origins | Same-origin SPA + API |

---

## Assumptions & Open Questions

| Assumption / decision | Chosen default | Rationale | Confirmed? |
| --------------------- | -------------- | --------- | ---------- |
| React placement | `resources/js` + existing Laravel Vite | User 1.A | y |
| Delivery depth | Structure only; no mockup port | User 2.A | y |
| Authentication | Never in this project | User 3.B + "projeto não terá autenticação" | y |
| Example API endpoint | `GET /api/health` returns JSON `{ "status": "ok" }` with HTTP 200 | Proves API wiring without domain logic | y — agent default |
| SPA shell URL | `GET /` returns Blade that mounts React | Matches current `web.php` root | y — agent default |
| HTTP client | Thin typed `fetch` wrapper under `resources/js/services` (`api.ts`) calling `/api` | TypeScript (AD-004); enough for academic demos | y — agent default; superseded JS helper |
| Mockup for this feature | Non-binding reference; UI deferred | User 2.A | y |

**Open questions:** none - all resolved or logged above.

---

## User Stories

### P1: React SPA shell ⭐ MVP

**User Story**: As a developer, I want React running through the project's Vite pipeline so that later screens can be built as React components instead of Blade or the static mockup HTML.

**Why P1**: Without a mounted SPA, the "frontend ReactJS com Vite" goal is unmet.

**Acceptance Criteria**:

1. WHEN a developer opens `/` THEN the system SHALL return an HTML document that loads the Vite React entry and mounts a root React element
2. The system SHALL compile React through Vite with `@vitejs/plugin-react` and a JS/JSX entry under `resources/js`
3. WHILE the SPA shell is loaded the system SHALL render a visible React-owned marker in the document body (so the mount is distinguishable from the old welcome Blade page)

**Independent Test**: Open `/` with Vite/dev or a production build and confirm the React marker is present; welcome Blade content is gone.

---

### P1: Public Laravel API surface ⭐ MVP

**User Story**: As a developer, I want `/api` routes that return JSON without authentication so that the React app can call the backend on the same origin.

**Why P1**: The product is "API Laravel + frontend React"; an empty web-only app does not deliver that.

**Acceptance Criteria**:

1. WHEN a client sends `GET /api/health` THEN the system SHALL respond with HTTP 200 and a JSON body containing `"status": "ok"`
2. The system SHALL register API routes from `routes/api.php` with the `/api` prefix
3. IF a client calls `/api/health` without credentials or session THEN the system SHALL still return the success response (no auth challenge)

**Independent Test**: `curl -i /api/health` returns 200 JSON without cookies or tokens.

---

### P1: Frontend layout and API client ⭐ MVP

**User Story**: As a developer, I want a predictable React folder layout and a shared API helper so that academic features can call `/api` without reinventing HTTP each time.

**Why P1**: Structure was the explicit deliverable.

**Acceptance Criteria**:

1. The system SHALL provide `resources/js` folders for `components`, `pages`, and `services` (empty placeholders allowed except for the required bootstrap files)
2. WHEN frontend code imports the shared API helper THEN it SHALL be able to request `/api/health` and receive the parsed JSON status payload
3. WHILE the SPA shell is shown the system SHALL use that helper at least once to display the health status text from the API (proves end-to-end wiring)

**Independent Test**: Load `/`, see React UI that reflects the `/api/health` response; helper module exists under `resources/js/services`.

---

## Edge Cases

- IF the API is unreachable from the browser THEN the React shell SHALL still mount and SHALL show a visible error state for the health check (not a blank page)
- IF an unknown `/api/*` path is requested THEN the system SHALL return a JSON error response (not the Blade SPA HTML)

---

## Requirement Traceability

| Requirement ID | Story | Phase | Status |
| -------------- | ----- | ----- | ------ |
| STRUCT-01 | P1: React SPA shell | - | Pending |
| STRUCT-02 | P1: React SPA shell | - | Pending |
| STRUCT-03 | P1: React SPA shell | - | Pending |
| STRUCT-04 | P1: Public Laravel API surface | - | Pending |
| STRUCT-05 | P1: Public Laravel API surface | - | Pending |
| STRUCT-06 | P1: Public Laravel API surface | - | Pending |
| STRUCT-07 | P1: Frontend layout and API client | - | Pending |
| STRUCT-08 | P1: Frontend layout and API client | - | Pending |
| STRUCT-09 | P1: Frontend layout and API client | - | Pending |
| STRUCT-10 | Edge: API unreachable | - | Pending |
| STRUCT-11 | Edge: unknown API path JSON | - | Pending |

**Coverage:** 11 total, 0 mapped to tasks, 11 unmapped

---

## Differences from the source

| Source shows | Product shows | Confirmed |
| ------------ | ------------- | --------- |
| Full booking UI (Dashboard, Salas, Minhas Reservas, modals) | Minimal React shell + health status only | y - AD-003 |

## Sources

- User decisions 1.A / 2.A / 3.B and academic no-auth constraint - settle placement, depth, auth
- ![room-booking-mockup](captures/room-booking-mockup.html) - non-binding - user: "2. A" (estrutura only; mockup UI deferred)

---

## Success Criteria

- [ ] `/` serves a React-mounted page (not Laravel welcome)
- [ ] `GET /api/health` returns 200 JSON without authentication
- [ ] React folder layout + API helper exist; shell displays health via the helper

## Checks

See `.specs/features/react-api-structure/tasks.md` `## Checks` (authoritative).

**Build**: `npm run build; vendor/bin/pint --test; php artisan test --filter=ReactApiStructure`

**Run feature**: `php artisan test --filter=ReactApiStructure --testdox`
