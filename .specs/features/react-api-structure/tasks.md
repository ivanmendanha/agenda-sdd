# React API Structure Tasks

## Execution Protocol (MANDATORY -- do not skip)

Implement these tasks with the `tlc-spec-driven` skill: **activate it by name and follow its Execute flow and Critical Rules.** Do not search for skill files by filesystem path. The skill is the source of truth for the full flow (per-task cycle, sub-agent delegation, the walk, check_proofs, the Verifier and the screens sub-agent).

**If the skill cannot be activated, STOP and tell the user - do not proceed without it.**

---

**Design**: none - Design phase skipped (straightforward scaffold)
**Status**: In Progress
**Approved**: 2026-11-04 - user: "aprovado"
**Build**: `npm run build; vendor/bin/pint --test; php artisan test`
**Run feature**: `php artisan test --filter=ReactApiStructure --testdox`

---

## Test Coverage Matrix

> Generated from codebase, project guidelines, and spec - confirm before Execute. Guidelines found: `AGENTS.md`, `.cursor/skills/testing-best-practices/SKILL.md` (PHPUnit feature tests; no Dusk).

| Code Layer | Required Test Type | Coverage Expectation | Location Pattern | Run Command |
| ---------- | ------------------ | -------------------- | ---------------- | ----------- |
| API routes / HTTP | feature | Happy path + unauthenticated access + unknown `/api/*` JSON | `tests/Feature/*ReactApiStructure*` | `php artisan test --filter=ReactApiStructure` |
| SPA Blade shell | feature | `/` returns React mount document (not welcome) | `tests/Feature/*ReactApiStructure*` | `php artisan test --filter=ReactApiStructure` |
| React client health UI / fetch error | none (no Dusk) | Proven by walk after last task | - | walk |
| Vite / package config | none | Build gate only | - | `npm run build` |

## Gate Check Commands

| Gate Level | When to Use | Command |
| ---------- | ----------- | ------- |
| Quick | After API/feature-test tasks | `php artisan test --filter=ReactApiStructure` |
| Full | After SPA shell tasks | `php artisan test --filter=ReactApiStructure` |
| Build | After toolchain / phase end | `npm run build; vendor/bin/pint --test; php artisan test --filter=ReactApiStructure` |

---

## Execution Plan

### Phase 1: Public API

```
T1 → T2
```

### Phase 2: React toolchain

```
T3 → T4
```

### Phase 3: SPA shell and client wiring

```
T4 → T5 → T6 → T7
T2 → T6
```

---

## Task Breakdown

### T1: Register Laravel API routes

**What**: Enable `routes/api.php` with the `/api` prefix in the application bootstrap.
**Where**: `bootstrap/app.php`
**Depends on**: None
**Reuses**: Existing `shouldRenderJsonWhen` for `api/*`
**Requirement**: STRUCT-05

**Done when**:

- [x] `withRouting` registers `api: __DIR__.'/../routes/api.php'` (or equivalent api prefix wiring)
- [x] Application boots without error

**Tests**: none - config/bootstrap only; HTTP coverage lands in T2
**Gate**: build — `php artisan route:list --path=api` exits 0 once T2 adds routes (after T1 alone, empty api file is OK)

---

### T2: Public health endpoint and API feature tests

**What**: Add unauthenticated `GET /api/health` returning JSON `{ "status": "ok" }` and feature tests for success, no-auth, and unknown API path JSON.
**Where**: `routes/api.php`
**Depends on**: T1
**Reuses**: Laravel JSON responses
**Requirement**: STRUCT-04, STRUCT-05, STRUCT-06, STRUCT-11

**Done when**:

- [ ] `GET /api/health` returns HTTP 200 and JSON with `"status":"ok"`
- [ ] Same response without session/credentials
- [ ] Unknown `/api/does-not-exist` returns a JSON error body (not HTML)
- [ ] Feature tests cover the above under `tests/Feature/ReactApiStructureTest.php`

**Tests**: feature — `tests/Feature/ReactApiStructureTest.php` (happy + no-auth + unknown path)
**Gate**: quick — `php artisan test --filter=ReactApiStructure`

---

### T3: Add React npm dependencies

**What**: Add `react`, `react-dom`, and `@vitejs/plugin-react` to the project package manifest and install them.
**Where**: `package.json`
**Depends on**: None (parallel with Phase 1 in plan order after T2 for simpler linear execute)
**Reuses**: Existing Vite 8 / laravel-vite-plugin stack
**Requirement**: STRUCT-02

**Done when**:

- [ ] `react`, `react-dom`, and `@vitejs/plugin-react` appear in `package.json` dependencies/devDependencies
- [ ] `npm install` completes successfully

**Tests**: none - package manifest; verified by Build after T4
**Gate**: build — `npm install` (install only; full `npm run build` after T4/T6)

---

### T4: Enable React in Vite config

**What**: Register `@vitejs/plugin-react` and point the Laravel Vite input at the React entry.
**Where**: `vite.config.js`
**Depends on**: T3
**Reuses**: Existing `laravel-vite-plugin` and Tailwind plugin
**Requirement**: STRUCT-02

**Done when**:

- [ ] `plugins` include the React plugin
- [ ] Laravel plugin `input` includes the React entry under `resources/js` (`.jsx` or `.tsx`)

**Tests**: none - config; Build gate after entry exists (T6)
**Gate**: build — config file updated; `npm run build` runs green after T6

---

### T5: Blade SPA shell at `/`

**What**: Replace the welcome page with a Blade document that provides a React mount node and loads the Vite React entry.
**Where**: `resources/views/app.blade.php`
**Depends on**: T4
**Reuses**: `@vite` Blade directive
**Requirement**: STRUCT-01, STRUCT-03

**Done when**:

- [ ] `GET /` is routed to `app` Blade view (update `routes/web.php` as part of this deliverable)
- [ ] Document contains a root mount element (e.g. `id="app"`)
- [ ] Document loads the Vite React entry via `@vite`
- [ ] Feature test asserts `/` is 200, contains the mount element, and does not contain the default Laravel welcome headline

**Tests**: feature — extend `tests/Feature/ReactApiStructureTest.php`
**Gate**: full — `php artisan test --filter=ReactApiStructure`

---

### T6: React folders, API helper, and App health UI

**What**: Create `resources/js` layout (`components`, `pages`, `services`), shared `/api` fetch helper, entry + `App` that shows health status or a visible error if the request fails.
**Where**: `resources/js/app.jsx`
**Depends on**: T2, T5
**Reuses**: `/api/health` from T2
**Requirement**: STRUCT-02, STRUCT-03, STRUCT-07, STRUCT-08, STRUCT-09, STRUCT-10

**Done when**:

- [ ] Folders `resources/js/components`, `resources/js/pages`, `resources/js/services` exist
- [ ] `resources/js/services/api.js` (or `.ts`) can request `/api/health` and return parsed JSON
- [ ] Mounted App uses the helper and displays the health status text on success
- [ ] When the health request fails, App still mounts and shows a visible error state
- [ ] `npm run build` succeeds

**Tests**: none - browser fetch UI has no Dusk in repo; covered by walk proofs for STRUCT-09/10; STRUCT-07 verified by folders existing at gate
**Gate**: build — `npm run build; php artisan test --filter=ReactApiStructure`

---

### T7: Confirm SPA/API wiring in feature suite

**What**: Finalize `ReactApiStructureTest` so STRUCT-01..06 and STRUCT-11 stay green after the React entry rename/removal of `app.js` if needed.
**Where**: `tests/Feature/ReactApiStructureTest.php`
**Depends on**: T6
**Reuses**: Existing feature test style in `tests/Feature/ExampleTest.php`
**Requirement**: STRUCT-01, STRUCT-04, STRUCT-05, STRUCT-06, STRUCT-11

**Done when**:

- [ ] Filter `ReactApiStructure` passes entirely
- [ ] Example welcome-only assumptions updated if `ExampleTest` would fail on `/`

**Tests**: feature — same file
**Gate**: build — `npm run build; vendor/bin/pint --test; php artisan test --filter=ReactApiStructure`

---

## Checks

**Build**: `npm run build; vendor/bin/pint --test; php artisan test`

**Run feature**: `php artisan test --filter=ReactApiStructure --testdox`

**C1** - `GET /` returns HTML that mounts React (STRUCT-01, AC 1)
Proof: run feature | Root serves React shell

**C2** - Vite compiles React via `@vitejs/plugin-react` and a JS/JSX entry (STRUCT-02, AC 2)
Proof: `npm run build`

**C3** - SPA shell shows a visible React-owned marker (STRUCT-03, AC 3)
Proof: run feature | Root serves React shell

**C4** - `GET /api/health` returns 200 JSON with status ok (STRUCT-04, AC 1)
Proof: run feature | Health endpoint returns ok

**C5** - API routes load from `routes/api.php` with `/api` prefix (STRUCT-05, AC 2)
Proof: run feature | Health endpoint returns ok

**C6** - `/api/health` succeeds without credentials (STRUCT-06, AC 3)
Proof: run feature | Health allows unauthenticated access

**C7** - `resources/js` has `components`, `pages`, and `services` (STRUCT-07, AC 1)
Proof: walk - folders present under `resources/js`

**C8** - Shared API helper can request `/api/health` (STRUCT-08, AC 2)
Proof: walk - `resources/js/services/api.js` exports health helper used by App

**C9** - Shell displays health status via the helper (STRUCT-09, AC 3)
Proof: walk - open `/` and see health status text from API

**C10** - API failure shows visible error; shell still mounts (STRUCT-10)
Proof: walk - with API stopped or forced failure, error text visible and root mounted

**C11** - Unknown `/api/*` returns JSON error (STRUCT-11)
Proof: run feature | Unknown API path returns JSON

---

## Handoff
