# App Layout Tasks

## Execution Protocol (MANDATORY -- do not skip)

Implement these tasks with the `tlc-spec-driven` skill: **activate it by name and follow its Execute flow and Critical Rules.** Do not search for skill files by filesystem path. The skill is the source of truth for the full flow (per-task cycle, sub-agent delegation, the walk, check_proofs, the Verifier and the screens sub-agent).

**If the skill cannot be activated, STOP and tell the user - do not proceed without it.**

---

**Design**: `.specs/features/app-layout/design.md`
**Status**: In Progress
**Approved**: 2026-11-04 - user: "aprovado"
**Build**: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`
**Run feature**: `php artisan test --filter=AppLayout --testdox`

---

## Test Coverage Matrix

> Generated from codebase, project guidelines, and spec - confirm before Execute. Guidelines found: `AGENTS.md`, `.agents/skills/testing-best-practices/SKILL.md` (PHPUnit feature tests; no Dusk).

| Code Layer | Required Test Type | Coverage Expectation | Location Pattern | Run Command |
| ---------- | ------------------ | -------------------- | ---------------- | ----------- |
| SPA web routes (deep link / unknown) | feature | `/`, `/salas`, `/minhas-reservas`, unknown path serve Blade `#app`; `/api/health` stays JSON | `tests/Feature/*AppLayout*` | `php artisan test --filter=AppLayout` |
| Layout chrome / tokens / nav active | none (no Dusk) | Proven by walk + browser capture proofs | - | walk / browser |
| Vite / CSS tokens / package.json | none | Build + typecheck gate | - | `npm run build; npm run typecheck` |

## Gate Check Commands

| Gate Level | When to Use | Command |
| ---------- | ----------- | ------- |
| Quick | After feature-test tasks | `php artisan test --filter=AppLayout` |
| Full | After SPA route + shell wiring | `php artisan test --filter=AppLayout; npm run typecheck` |
| Build | After dependency / CSS / phase end | `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test` |

---

## Execution Plan

### Phase 1: Foundation through layout chrome

```
T1 → T2 → T3 → T4
```

### Phase 2: Pages, bootstrap, Laravel routes

```
T4 → T5 → T6 → T7
```

---

## Task Breakdown

### T1: Add mockup design tokens to global CSS

**What**: Define the mockup CSS custom properties and page background on `:root` / `body` in the global stylesheet.
**Where**: `resources/css/app.css`
**Depends on**: None
**Reuses**: Existing Tailwind `@import` and `@theme` block
**Requirement**: LAY-10, LAY-11

**Done when**:

- [x] `:root` defines at least `--primary` `#4A90E2`, `--bg-primary` `#FFFFFF`, `--bg-secondary` `#F5F7FA`, `--text-primary` `#262626`, `--text-secondary` `#595959`, `--border` `#D9D9D9`, and the header `--shadow`
- [x] `body` uses `--bg-secondary` as background and `--text-primary` as color

**Tests**: none - CSS/config; matrix row "Vite / CSS tokens / package.json"
**Gate**: build — `npm run build`

---

### T2: Add react-router-dom dependency

**What**: Add `react-router-dom` to the project and install it so tab navigation can sync with URLs.
**Where**: `package.json`
**Depends on**: T1
**Reuses**: Existing Vite React toolchain
**Requirement**: LAY-06, LAY-07, LAY-08

**Done when**:

- [x] `react-router-dom` appears in `package.json` dependencies
- [x] `npm install` completes successfully

**Tests**: none - package manifest; matrix row "Vite / CSS tokens / package.json"
**Gate**: build — `npm install`

---

### T3: Create AppLayout topbar and content frame

**What**: Build the shared layout component with topbar logo and centered max-width content container using the design tokens.
**Where**: `resources/js/components/AppLayout.tsx`
**Depends on**: T2
**Reuses**: Tokens from T1; children slot for nav + outlet
**Requirement**: LAY-01, LAY-02, LAY-03, LAY-04, LAY-12
**Capture**: `.specs/features/app-layout/captures/room-booking-layout.html`

**Done when**:

- [x] Topbar renders logo text exactly `📅 Reserva de Salas` colored with `--primary`
- [x] Topbar uses `--bg-primary` and the header shadow token
- [x] Content frame is centered with max-width 1200px and horizontal padding matching the capture
- [x] Component accepts `children` rendered below the topbar inside the frame structure ready for nav + outlet

**Tests**: none - layout chrome; matrix row "Layout chrome / tokens / nav active"
**Gate**: build — `npm run typecheck`

---

### T4: Create NavTabs with active route styling

**What**: Build the three primary nav tabs as router links with mockup active styles tied to the current path.
**Where**: `resources/js/components/NavTabs.tsx`
**Depends on**: T3
**Reuses**: `react-router-dom` `NavLink`; mockup tab labels
**Requirement**: LAY-05, LAY-06, LAY-07, LAY-08, LAY-09
**Capture**: `.specs/features/app-layout/captures/room-booking-layout.html`

**Done when**:

- [x] Tabs labeled exactly `Dashboard`, `Salas`, and `Minhas Reservas`
- [x] Links target `/`, `/salas`, and `/minhas-reservas` respectively
- [x] Active tab uses the mockup active style (primary color + bottom border)
- [x] Direct load of `/salas` or `/minhas-reservas` marks the matching tab active

**Tests**: none - layout chrome; matrix row "Layout chrome / tokens / nav active"
**Gate**: build — `npm run typecheck`

---

### T5: Add placeholder pages and not-found page

**What**: Create minimal placeholder page components for the three tabs and an unknown-route placeholder with no mockup domain widgets.
**Where**: `resources/js/pages/DashboardPage.tsx`
**Depends on**: T4
**Reuses**: Page folder from `react-api-structure`
**Requirement**: LAY-13, LAY-14, LAY-15

**Done when**:

- [x] `DashboardPage`, `SalasPage`, `MinhasReservasPage`, and `NotFoundPage` exist under `resources/js/pages`
- [x] None of them render mockup dashboard stats, rooms grid, or reservations list UI
- [x] Each is a minimal placeholder suitable for the content outlet

**Tests**: none - layout chrome placeholders; matrix row "Layout chrome / tokens / nav active"
**Gate**: build — `npm run typecheck`

---

### T6: Wire BrowserRouter and AppLayout in the React entry

**What**: Mount `BrowserRouter` with layout-wrapped routes for the three tabs and a catch-all not-found route; remove the health-check shell from the main UI.
**Where**: `resources/js/app.tsx`
**Depends on**: T5
**Reuses**: `AppLayout`, `NavTabs`, placeholder pages; keep `services/api.ts` unused by chrome
**Requirement**: LAY-01, LAY-04, LAY-13, LAY-14, LAY-15
**Capture**: `.specs/features/app-layout/captures/room-booking-layout.html`

**Done when**:

- [x] Entry renders `BrowserRouter` with routes `/`, `/salas`, `/minhas-reservas`, and `*`
- [x] Shared layout (topbar + nav + outlet) wraps matched routes
- [x] Health status / health error UI is not rendered on the main shell
- [x] `npm run build` and `npm run typecheck` succeed

**Tests**: none - layout chrome; matrix row "Layout chrome / tokens / nav active"
**Gate**: build — `npm run build; npm run typecheck`

---

### T7: SPA fallback web routes and AppLayout feature tests

**What**: Serve the Blade SPA shell for client deep links and unknown non-API paths, and add PHPUnit feature tests proving those documents mount React while `/api/health` stays JSON.
**Where**: `routes/web.php`
**Depends on**: T6
**Reuses**: `resources/views/app.blade.php`; existing feature-test style
**Requirement**: LAY-01, LAY-09, LAY-15

**Done when**:

- [ ] `GET /`, `/salas`, `/minhas-reservas`, and an unknown non-API path return 200 HTML containing `id="app"`
- [ ] `GET /api/health` still returns JSON `{ "status": "ok" }`
- [ ] Feature tests live in `tests/Feature/AppLayoutTest.php` and pass under `--filter=AppLayout`

**Tests**: feature — `tests/Feature/AppLayoutTest.php` (happy deep links + unknown path shell + API still JSON)
**Gate**: build — `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test --filter=AppLayout`


---

## Checks

**Build**: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Run feature**: `php artisan test --filter=AppLayout --testdox`

**C1** - Any SPA route shows topbar + content frame as in the capture (LAY-01, AC 1)
Proof: browser captures/room-booking-layout.running.png

**C2** - Topbar logo text is exactly `📅 Reserva de Salas` (LAY-02, AC 2)
Proof: walk - open `/` and read the topbar logo text

**C3** - Main content uses centered max-width 1200px container with horizontal padding (LAY-03, AC 3)
Proof: browser captures/room-booking-layout.running.png

**C4** - Topbar stays above the content area on every SPA route (LAY-04, AC 4)
Proof: walk - open `/`, `/salas`, `/minhas-reservas`; topbar above content on each

**C5** - Three nav tabs labeled Dashboard, Salas, Minhas Reservas (LAY-05, AC 1)
Proof: browser captures/room-booking-layout.running.png

**C6** - Activating Dashboard navigates to `/` with active tab style (LAY-06, AC 2)
Proof: walk - click Dashboard; URL `/` and active tab style

**C7** - Activating Salas navigates to `/salas` with active tab style (LAY-07, AC 3)
Proof: walk - click Salas; URL `/salas` and active tab style

**C8** - Activating Minhas Reservas navigates to `/minhas-reservas` with active tab style (LAY-08, AC 4)
Proof: walk - click Minhas Reservas; URL `/minhas-reservas` and active tab style

**C9** - Deep link to `/salas` or `/minhas-reservas` marks the matching tab active (LAY-09, AC 5)
Proof: walk - open `/salas` and `/minhas-reservas` directly; matching tab is active on each

**C10** - Required design tokens are defined with mockup values (LAY-10, AC 1)
Proof: walk - `resources/css/app.css` defines listed token hex values

**C11** - Page background uses `--bg-secondary` and topbar `--bg-primary` + header shadow (LAY-11, AC 2)
Proof: browser captures/room-booking-layout.running.png

**C12** - Logo uses `--primary` color (LAY-12, AC 3)
Proof: browser captures/room-booking-layout.running.png

**C13** - Route content renders inside the shared content area below nav (LAY-13, AC 1)
Proof: walk - placeholder content appears below nav tabs inside the container

**C14** - Mockup domain widgets are absent from this feature (LAY-14, AC 2)
Proof: walk - no Reservas de Hoje stats cards, rooms grid, or Minhas Reservas list widgets

**C15** - Unknown SPA path keeps chrome and shows not-found placeholder (LAY-15, AC 3)
Proof: run feature | Spa shell serves unknown path

---

## Handoff
