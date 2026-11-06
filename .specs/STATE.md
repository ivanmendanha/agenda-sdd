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

### AD-006
- **Decision**: Any visitor can create, edit, activate, and deactivate rooms. There is no admin role and no login.
- **Reason**: Question: "O que esta spec deve cobrir?" Option chosen: "O PDF inteiro, inclusive cadastrar/editar/ativar/inativar salas, sem login: qualquer visitante faz o CRUD (afasta CA08 e AD-001)."
- **Trade-off**: Departs from HU-002 CA08. AD-001 stays in force: the product still has no authentication.
- **Scope**: `salas` feature
- **Date**: 2026-11-06
- **Status**: AD-006 active

### AD-007
- **Decision**: The visitor starts and completes a reservation on its own page. This feature has no maintenance block.
- **Reason**: Question: "Onde começa e se conclui a reserva?" Option chosen: "Página própria de reserva, não o modal. Sem bloqueio de manutenção."
- **Trade-off**: Departs from the mockup, where clicking a room opens the Nova Reserva modal.
- **Scope**: `salas` feature reservation flow
- **Date**: 2026-11-06
- **Status**: AD-007 active

### AD-008
- **Decision**: The room list shows inactive rooms, marked inactive, and refuses a new reservation for them.
- **Reason**: Question: "Quem vê sala inativa?" Option chosen: "A consulta também mostra salas inativas, marcadas como inativas, e bloqueia nova reserva nelas."
- **Trade-off**: The mockup list only shows bookable rooms under `Salas Disponíveis`. The product heading is `Salas`.
- **Scope**: `salas` feature consultation
- **Date**: 2026-11-06
- **Status**: AD-008 active

### AD-009
- **Decision**: The list filters by minimum capacity and by resource. Availability is a free interval (date, start, end) from 08:00 to 18:00, with a maximum duration of 4 hours. There is no location filter and no predefined slot grid.
- **Reason**: Question: "Como a pessoa encontra uma sala e checa se está livre?" Option chosen: "Filtros de capacidade mínima e recurso. Disponibilidade por intervalo livre (data + início + fim), 08:00–18:00, duração máxima de 4 horas. Sem localização e sem horários pré-definidos."
- **Trade-off**: The mockup list has neither filters nor availability marks.
- **Scope**: `salas` feature consultation
- **Date**: 2026-11-06
- **Status**: AD-009 active

### AD-010
- **Decision**: This feature does not enforce a limit of 3 active reservations.
- **Reason**: HU-002 does not require that limit. The spec plan recorded it as a default, and the user confirmed the plan: "Implement the plan as specified".
- **Trade-off**: A visitor can hold more than 3 confirmed reservations. The mockup alert `Limite de 3 reservas ativas excedido` is absent.
- **Scope**: `salas` feature
- **Date**: 2026-11-06
- **Status**: AD-010 active

### AD-011
- **Decision**: The sqlite connection uses `transaction_mode` `IMMEDIATE` and `busy_timeout` `5000`. Creating a reservation runs inside `DB::transaction` and calls `lockForUpdate` on the room row for drivers that emit a row lock.
- **Reason**: User chose "Laravel decide tudo; o React só exibe. Filtro, disponibilidade e conflito ficam na API. A criação concorrente trava a sala numa transação, o que funciona no SQLite." Laravel's `SQLiteGrammar::compileLock` returns an empty string, so `lockForUpdate` does not lock on sqlite. An `IMMEDIATE` transaction takes the writer lock before the overlap read. `busy_timeout` lets the second writer wait.
- **Trade-off**: Every sqlite transaction in this application begins as `IMMEDIATE`, so a long transaction blocks other writers.
- **Scope**: `config/database.php` sqlite connection and reservation create
- **Date**: 2026-11-06
- **Status**: AD-011 active

## Handoff

- **Feature**: app-layout
- **Phase**: Done — validate_verification green
- **Completed**: T1–T7, walk, screens matches, verification findings closed
- **Next**: Domain page features (Dashboard / Salas / Minhas Reservas) against the shared chrome
- **Branch**: master
