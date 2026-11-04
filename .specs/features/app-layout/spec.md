# App Layout Specification

**Approved**: 2026-11-04 - user: "aprovado"

## Problem Statement

The React SPA still shows a developer health-check shell. Academic demos need the shared chrome from the room-booking mockup (topbar, nav, content frame, and visual tokens) so later page features can drop into a consistent layout instead of rebuilding it each time.

## Goals

- [ ] Every SPA route renders the mockup topbar with the exact logo text
- [ ] Primary nav tabs switch between Dashboard, Salas, and Minhas Reservas routes
- [ ] Design tokens from the mockup drive the chrome colors, type, and surfaces
- [ ] Page body is a content outlet with placeholders only (no domain UI yet)

## Out of Scope

| Feature | Reason |
| ------- | ------ |
| Dashboard cards, today's reservations, stats, "Nova Reserva" | User 5.A — body is outlet only; domain UI is later features |
| Rooms grid, room cards, feature tags | Deferred page feature |
| Minhas Reservas table and cancel/edit actions | Deferred page feature |
| Reservation modals and forms | Deferred page feature |
| Authentication / user menu in the topbar | AD-001 — product has no auth |
| API health status on the shell | User 5.A — removed from main UI |

---

## Assumptions & Open Questions

| Assumption / decision | Chosen default | Rationale | Confirmed? |
| --------------------- | -------------- | --------- | ---------- |
| Layout scope | Topbar + nav-tabs + content area | User 1.A | y |
| Nav behavior | React routes `/`, `/salas`, `/minhas-reservas` with placeholders | User 2.A | y |
| Logo | Exact string `📅 Reserva de Salas` | User 3 — keep | y |
| Design tokens | Extract mockup CSS variables in this feature | User 4.A | y |
| Body content | Route outlet / minimal placeholder; no domain mockup body | User 5.A | y |
| Header position | Normal document flow (not sticky) | Mockup does not sticky the header | y — agent default |
| Active tab | Tab matching the current route uses the mockup `.nav-tab.active` look | Needed for navigable shell | y — agent default |
| Route labels | Dashboard → `/`, Salas → `/salas`, Minhas Reservas → `/minhas-reservas` | Match mockup tab labels; Portuguese paths for Salas/Minhas Reservas | y — agent default |
| Auth / unauthorized chrome | N/A | AD-001 — no authentication | y |
| Loading / error for layout chrome | Layout is static; no async chrome | Tokens and structure are local | y — agent default |

**Open questions:** none - all resolved or logged above.

---

## User Stories

### P1: Topbar and content frame ⭐ MVP

**User Story**: As a visitor, I want a shared topbar and content frame on every screen so that the app feels like the room-booking product, not a blank API shell.

**Why P1**: Without the chrome, later pages have no common place to live and the mockup look is unmet.

**Acceptance Criteria**:

1. WHEN a visitor opens any SPA application route THEN the system SHALL show the topbar and content frame composition in `captures/room-booking-layout.html`
2. The system SHALL render the topbar logo text exactly as `📅 Reserva de Salas`
3. The system SHALL provide a main content area inside a centered container with max-width 1200px and horizontal padding matching `captures/room-booking-layout.html`
4. WHILE any SPA application route is shown the system SHALL keep the topbar visible above the content area

**Independent Test**: Open `/`, `/salas`, and `/minhas-reservas`; each shows the same topbar logo and framed content area.

---

### P1: Primary navigation tabs ⭐ MVP

**User Story**: As a visitor, I want Dashboard, Salas, and Minhas Reservas tabs so that I can move between the three main sections of the app.

**Why P1**: The mockup places these tabs on every screen; the shell is incomplete without them.

**Acceptance Criteria**:

1. WHEN a visitor opens any SPA application route THEN the system SHALL show the three nav tabs labeled `Dashboard`, `Salas`, and `Minhas Reservas` as in `captures/room-booking-layout.html`
2. WHEN a visitor activates the `Dashboard` tab THEN the system SHALL navigate to `/` and show that tab in the active style from `captures/room-booking-layout.html`
3. WHEN a visitor activates the `Salas` tab THEN the system SHALL navigate to `/salas` and show that tab in the active style from `captures/room-booking-layout.html`
4. WHEN a visitor activates the `Minhas Reservas` tab THEN the system SHALL navigate to `/minhas-reservas` and show that tab in the active style from `captures/room-booking-layout.html`
5. WHEN a visitor lands directly on `/salas` or `/minhas-reservas` THEN the system SHALL mark the matching tab active without requiring a prior click

**Independent Test**: Click each tab and deep-link each path; URL and active tab stay in sync.

---

### P1: Design tokens ⭐ MVP

**User Story**: As a visitor, I want the shell colors and surfaces to match the mockup so that later screens inherit one visual system.

**Why P1**: User chose to extract tokens now (4.A); chrome without tokens will drift from the binding source.

**Acceptance Criteria**:

1. The system SHALL define the mockup token values for at least `--primary` (`#4A90E2`), `--bg-primary` (`#FFFFFF`), `--bg-secondary` (`#F5F7FA`), `--text-primary` (`#262626`), `--text-secondary` (`#595959`), `--border` (`#D9D9D9`), and the standard shadow used by the header
2. WHILE the app layout is rendered the system SHALL paint the page background with `--bg-secondary` and the topbar surface with `--bg-primary` plus the header shadow from `captures/room-booking-layout.html`
3. WHILE the app layout is rendered the system SHALL color the logo with `--primary`

**Independent Test**: Inspect computed styles on body, header, and logo; values match the listed tokens.

---

### P1: Content outlet placeholders ⭐ MVP

**User Story**: As a developer, I want each nav route to render inside the shared content area with a minimal placeholder so that page features can replace the placeholder later without changing the chrome.

**Why P1**: User 5.A requires an empty/minimal body, not the mockup domain content.

**Acceptance Criteria**:

1. WHEN a visitor is on `/`, `/salas`, or `/minhas-reservas` THEN the system SHALL render that route's content inside the shared content area below the nav tabs
2. The system SHALL NOT render the mockup Dashboard reservation table, stats cards, rooms grid, or Minhas Reservas list in this feature
3. WHEN a visitor opens an unknown SPA path under the app shell THEN the system SHALL still show the shared topbar and nav and SHALL show a not-found placeholder in the content area

**Independent Test**: Visit the three routes and an unknown path; chrome remains; domain widgets from the mockup body are absent.

---

## Edge Cases

- IF client-side navigation fails to match a known tab route THEN the system SHALL keep the topbar and nav visible and SHALL show the not-found placeholder in the content area
- WHEN the viewport width is at most 768px THEN the system SHALL keep the topbar logo and the three nav tabs usable (wrapping or stacking allowed; no tab may be clipped out of reach)
- IF JavaScript fails before React mounts THEN the system SHALL still deliver the Blade/HTML shell document (existing SPA mount contract from `react-api-structure`); layout criteria apply after mount

---

## Requirement Traceability

| Requirement ID | Story | Phase | Status |
| -------------- | ----- | ----- | ------ |
| LAY-01 | P1: Topbar and content frame | Design | Pending |
| LAY-02 | P1: Topbar and content frame | Design | Pending |
| LAY-03 | P1: Topbar and content frame | Design | Pending |
| LAY-04 | P1: Topbar and content frame | Design | Pending |
| LAY-05 | P1: Primary navigation tabs | Design | Pending |
| LAY-06 | P1: Primary navigation tabs | Design | Pending |
| LAY-07 | P1: Primary navigation tabs | Design | Pending |
| LAY-08 | P1: Primary navigation tabs | Design | Pending |
| LAY-09 | P1: Primary navigation tabs | Design | Pending |
| LAY-10 | P1: Design tokens | Implement | Verified |
| LAY-11 | P1: Design tokens | Implement | Verified |
| LAY-12 | P1: Design tokens | Design | Pending |
| LAY-13 | P1: Content outlet placeholders | Design | Pending |
| LAY-14 | P1: Content outlet placeholders | Design | Pending |
| LAY-15 | P1: Content outlet placeholders | Design | Pending |

**ID format:** `LAY-[NUMBER]`

**Status values:** Pending → In Design → In Tasks → Implementing → Verified

**Coverage:** 15 total, 0 mapped to tasks, 15 unmapped

---

## Differences from the source

| Source shows | Product shows | Confirmed |
| ------------ | ------------- | --------- |
| Dashboard body: "Reservas de Hoje", Nova Reserva, stats cards | Minimal placeholder in the content outlet | y - AD-005 |
| Salas body: room cards grid | Minimal placeholder in the content outlet | y - AD-005 |
| Minhas Reservas body: reservations list | Minimal placeholder in the content outlet | y - AD-005 |
| Reservation modals | Nothing in this feature | y - AD-005 |

## Sources

- User decisions 2026-11-04: layout scope 1.A, routes 2.A, logo keep, tokens 4.A, outlet body 5.A
- AD-001 — no authentication chrome
- AD-002 — React inside Laravel Vite
- AD-004 — TypeScript React sources
- AD-005 — body content is outlet/placeholder, not mockup domain UI
- ![room-booking-layout](captures/room-booking-layout.html) - binding - topbar with logo `📅 Reserva de Salas`, nav tabs Dashboard / Salas / Minhas Reservas, centered content container, design tokens and chrome surfaces (page body domain widgets excluded per AD-005)

---

## Success Criteria

- [ ] Opening `/` shows the mockup topbar, tokens, and nav with Dashboard active
- [ ] Activating each tab updates the URL and active style
- [ ] No mockup domain widgets appear in the content area in this feature
- [ ] Visual chrome matches `captures/room-booking-layout.html` for topbar, nav, and container frame
