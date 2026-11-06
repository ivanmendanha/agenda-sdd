# Salas Tasks

## Execution Protocol (MANDATORY -- do not skip)

Implement these tasks with the `tlc-spec-driven` skill: **activate it by name and follow its Execute flow and Critical Rules.** Do not search for skill files by filesystem path. The skill is the source of truth for the full flow (per-task cycle, sub-agent delegation, the walk, check_proofs, the Verifier and the screens sub-agent).

**If the skill cannot be activated, STOP and tell the user - do not proceed without it.**

---

**Design**: `.specs/features/salas/design.md`
**Status**: Approved
**Approved**: 2026-11-06 - user: "Aprovado"
**Build**: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

---

## Test Coverage Matrix

> Generated from codebase, project guidelines, and spec - confirm before Execute. Guidelines found: `AGENTS.md`, `.cursor/skills/testing-best-practices/SKILL.md`, `phpunit.xml` (Pest feature tests, sqlite `:memory:`). No Dusk and no browser plugin.

| Code Layer | Required Test Type | Coverage Expectation | Location Pattern | Run Command |
| ---------- | ------------------ | -------------------- | ---------------- | ----------- |
| Domain (`IntervalValidator`, `CreateReservation`, `ListRooms`) | feature | Every branch that maps to a spec AC, including the edge cases in spec.md | `tests/Feature/Salas/*` | `php artisan test tests/Feature/Salas --testdox` |
| HTTP routes for rooms and reservations | feature | Each route: success, validation, conflict, unknown room, and no credentials | `tests/Feature/Salas/*` | `php artisan test tests/Feature/Salas --testdox` |
| Eloquent models, factories, migrations | none | Build gate. Behavior is covered through the feature tests above | `app/Models/`, `database/` | build gate |
| Config (`database.php` sqlite, `config/reservas.php`) | none | Build gate | `config/` | build gate |
| React pages and `api.ts` | none | No component runner installed. Visible states are walk or browser proofs | `resources/js/` | `npm run typecheck` |

## Gate Check Commands

> Generated from codebase - confirm before Execute.

| Gate Level | When to Use | Command |
| ---------- | ----------- | ------- |
| Quick | After a domain or HTTP task | `php artisan test tests/Feature/Salas --testdox` |
| Full | After a route task | `vendor/bin/pint --dirty --format agent; php artisan test tests/Feature/Salas --testdox` |
| Build | After config, schema, or React tasks, and at phase end | `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test` |

---

## Execution Plan

Phases run in order. Each task depends only on the one before it.

### Phase 1: Connection and rooms

```
T1 → T2 → T3 → T4 → T5 → T6
```

### Phase 2: Reservations and factories

```
T6 → T7 → T8 → T9 → T10
```

### Phase 3: Domain rules

```
T10 → T11 → T12 → T13
```

### Phase 4: Room HTTP

```
T13 → T14 → T15 → T16 → T17 → T18 → T19 → T20 → T21
```

### Phase 5: Reservation HTTP

```
T21 → T22
```

### Phase 6: React screens

```
T22 → T23 → T24 → T25 → T26 → T27 → T28
```

---

## Task Breakdown

### T1: Serialize sqlite writers

**What**: Set the sqlite connection to `IMMEDIATE` transactions and a 5000 ms busy timeout.
**Where**: `config/database.php`
**Depends on**: None
**Reuses**: The existing sqlite connection block
**Requirement**: SALA-59

**Done when**:

- [x] `transaction_mode` is `IMMEDIATE`
- [x] `busy_timeout` is `5000`
- [x] Gate check passes: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Tests**: none - config layer
**Gate**: build

**Commit**: `feat(salas): serialize sqlite writes with immediate transactions`

---

### T2: Centralize reservation limits

**What**: Add the shared business-hours and duration settings.
**Where**: `config/reservas.php`
**Depends on**: T1
**Reuses**: Laravel config files
**Requirement**: SALA-42

**Done when**:

- [ ] `opens_at` is `08:00`, `closes_at` is `18:00`, and `max_duration_minutes` is `240`
- [ ] Gate check passes: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Tests**: none - config layer
**Gate**: build

**Commit**: `feat(salas): centralize business hours and maximum duration`

---

### T3: Create the rooms table

**What**: Add the rooms migration via Artisan.
**Where**: `database/migrations/create_rooms_table.php`
**Depends on**: T2
**Reuses**: `php artisan make:migration`
**Requirement**: SALA-65

**Done when**:

- [ ] Columns are `name` (string 100), `capacity` (unsigned small integer), `active` (boolean, default true), and timestamps
- [ ] Gate check passes: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Tests**: none - schema layer
**Gate**: build

**Commit**: `feat(salas): add the rooms table`

---

### T4: Add the Room model

**What**: Add the Room Eloquent model with fillable fields, the active cast, and resource and reservation relationships.
**Where**: `app/Models/Room.php`
**Depends on**: T3
**Reuses**: `php artisan make:model`
**Requirement**: SALA-65

**Done when**:

- [ ] `resources()` and `reservations()` are declared with return types
- [ ] `active` is cast to boolean
- [ ] Gate check passes: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Tests**: none - entity layer
**Gate**: build

**Commit**: `feat(salas): add the Room model`

---

### T5: Create the room resource tags table

**What**: Add the `room_resources` migration via Artisan.
**Where**: `database/migrations/create_room_resources_table.php`
**Depends on**: T4
**Reuses**: `php artisan make:migration`
**Requirement**: SALA-70

**Done when**:

- [ ] `room_id` is a foreign key that cascades on delete
- [ ] Unique index is `(room_id, name_normalized)`
- [ ] Gate check passes: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Tests**: none - schema layer
**Gate**: build

**Commit**: `feat(salas): add room resource tags`

---

### T6: Add the RoomResource model

**What**: Add the tag model and the inverse room relationship.
**Where**: `app/Models/RoomResource.php`
**Depends on**: T5
**Reuses**: `Room`
**Requirement**: SALA-70

**Done when**:

- [ ] `room()` is a `BelongsTo`
- [ ] `name` and `name_normalized` are fillable
- [ ] Gate check passes: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Tests**: none - entity layer
**Gate**: build

**Commit**: `feat(salas): add the RoomResource model`

---

### T7: Create the reservations table

**What**: Add the reservations migration via Artisan.
**Where**: `database/migrations/create_reservations_table.php`
**Depends on**: T6
**Reuses**: `php artisan make:migration`
**Requirement**: SALA-45

**Done when**:

- [ ] `room_id` restricts on delete
- [ ] `starts_at` and `ends_at` are `char(5)`, `subject` is string 200, `status` defaults to `confirmed`
- [ ] Index is `(room_id, date, status)`
- [ ] Gate check passes: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Tests**: none - schema layer
**Gate**: build

**Commit**: `feat(salas): add the reservations table`

---

### T8: Add the Reservation model

**What**: Add the Reservation model, status cast, and room relationship.
**Where**: `app/Models/Reservation.php`
**Depends on**: T7
**Reuses**: `Room`
**Requirement**: SALA-45

**Done when**:

- [ ] `room()` is a `BelongsTo`
- [ ] `date` is cast to a date
- [ ] Gate check passes: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Tests**: none - entity layer
**Gate**: build

**Commit**: `feat(salas): add the Reservation model`

---

### T9: Add the Room factory

**What**: Add a Room factory with an inactive state.
**Where**: `database/factories/RoomFactory.php`
**Depends on**: T8
**Reuses**: `php artisan make:factory`
**Requirement**: SALA-65

**Done when**:

- [ ] Default room is active, with a name and a capacity from 1 to 999
- [ ] `inactive()` sets `active` to false
- [ ] Gate check passes: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Tests**: none - factory layer
**Gate**: build

**Commit**: `feat(salas): add the Room factory`

---

### T10: Add the Reservation factory

**What**: Add a Reservation factory with confirmed and unconfirmed states.
**Where**: `database/factories/ReservationFactory.php`
**Depends on**: T9
**Reuses**: `RoomFactory`
**Requirement**: SALA-61

**Done when**:

- [ ] Default status is `confirmed`, times are zero-padded `H:i` inside 08:00–18:00
- [ ] `unconfirmed()` sets status to `unconfirmed`
- [ ] Gate check passes: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Tests**: none - factory layer
**Gate**: build

**Commit**: `feat(salas): add the Reservation factory`

---

### T11: Validate an interval once

**What**: Add `IntervalValidator` and feature tests for the ordered business checks.
**Where**: `app/Reservations/IntervalValidator.php`
**Depends on**: T10
**Reuses**: `config/reservas.php` and `config('app.timezone')`
**Requirement**: SALA-36, SALA-37, SALA-38, SALA-39, SALA-40, SALA-41

**Done when**:

- [ ] `firstFailure` returns the spec sentence for the first failure only, in the spec order, or null
- [ ] `tests/Feature/Salas/IntervalValidatorTest.php` uses `RefreshDatabase` only if it touches the database, and these tests pass: `validator rejects an end that is not after the start`, `validator rejects a date before today`, `validator rejects an interval outside business hours`, `validator rejects a duration over 4 hours`, `validator reports only the first failing check`, `validator accepts a duration of exactly 4 hours`, `validator accepts a start of 08:00`, `validator accepts an end of 18:00`
- [ ] Test count: 8 tests pass
- [ ] Gate check passes: `php artisan test tests/Feature/Salas --testdox`

**Tests**: feature
**Gate**: quick

**Commit**: `feat(salas): reject invalid intervals in one order`

---

### T12: Create a reservation inside a transaction

**What**: Add `CreateReservation` and feature tests for conflict, inactive rooms, touching intervals, unconfirmed rows, and two concurrent creates.
**Where**: `app/Reservations/CreateReservation.php`
**Depends on**: T11
**Reuses**: `IntervalValidator`, `DB::transaction`, `lockForUpdate`
**Requirement**: SALA-45, SALA-49, SALA-50, SALA-59, SALA-60, SALA-61

**Done when**:

- [ ] The write rolls back on conflict and on an inactive room, and stores nothing partial
- [ ] `tests/Feature/Salas/CreateReservationTest.php` passes these tests: `action stores one confirmed reservation`, `action refuses an inactive room`, `action refuses an overlapping confirmed reservation`, `action allows a touching reservation`, `action ignores an unconfirmed reservation`, `action confirms exactly one concurrent overlap`
- [ ] The concurrent test uses two PHP processes and one sqlite file, not two sequential calls in one process
- [ ] Test count: 6 tests pass
- [ ] Gate check passes: `php artisan test tests/Feature/Salas --testdox`

**Tests**: feature
**Gate**: quick

**Commit**: `feat(salas): store a reservation under an immediate transaction`

---

### T13: Query rooms, filters, and availability

**What**: Add `ListRooms` and feature tests for filters and availability marks.
**Where**: `app/Rooms/ListRooms.php`
**Depends on**: T12
**Reuses**: `IntervalValidator`, `Room`, `RoomResource`
**Requirement**: SALA-05, SALA-18, SALA-19, SALA-20, SALA-23, SALA-26, SALA-28, SALA-29, SALA-30, SALA-31, SALA-32, SALA-33

**Done when**:

- [ ] Active rooms receive `disponivel` or `indisponivel` only after a valid interval. Inactive rooms stay in the list with no availability flag
- [ ] `tests/Feature/Salas/ListRoomsTest.php` passes these tests: `query returns every stored room`, `query filters by minimum capacity`, `query filters by resource ignoring case`, `query applies capacity and resource together`, `query returns every room when filters are blank`, `query marks an overlapping active room indisponivel`, `query marks a free active room disponivel`, `query leaves an inactive room without availability`, `query treats a touching reservation as free`, `query ignores another room`, `query ignores another date`
- [ ] Test count: 11 tests pass
- [ ] Gate check passes: `php artisan test tests/Feature/Salas --testdox`

**Tests**: feature
**Gate**: quick

**Commit**: `feat(salas): filter rooms and mark availability`

---

### T14: Shape the room JSON

**What**: Add the Eloquent API resource for a room.
**Where**: `app/Http/Resources/RoomResource.php`
**Depends on**: T13
**Reuses**: `php artisan make:resource`
**Requirement**: SALA-11

**Done when**:

- [ ] Payload includes `id`, `name`, `capacity`, `resources` as strings, `active`, and `availability` null unless the query set it
- [ ] Gate check passes: `vendor/bin/pint --dirty --format agent; php artisan test tests/Feature/Salas --testdox`

**Tests**: feature
**Gate**: full

**Commit**: `feat(salas): add the room JSON resource`

---

### T15: Create a room over HTTP

**What**: Add `POST /api/rooms` with validation, deduped tags, and no delete route.
**Where**: `app/Http/Controllers/RoomController.php`
**Depends on**: T14
**Reuses**: `routes/api.php`, `StoreRoomRequest`
**Requirement**: SALA-65, SALA-66, SALA-67, SALA-68, SALA-69, SALA-70, SALA-71, SALA-74

**Done when**:

- [ ] HTTP 201 returns the room resource. Invalid name or capacity returns HTTP 422 with the spec sentence and writes no row
- [ ] `tests/Feature/Salas/RoomApiTest.php` passes these tests: `it creates a room`, `it rejects an empty room name`, `it rejects a room name over 100 characters`, `it rejects a capacity outside 1 to 999`, `it stores a room without resources`, `it stores one tag for a repeated resource`, `it allows two rooms to share a name`, `it does not expose a room delete route`
- [ ] Test count: 8 tests pass in this file, plus the earlier Salas tests
- [ ] Gate check passes: `vendor/bin/pint --dirty --format agent; php artisan test tests/Feature/Salas --testdox`

**Tests**: feature
**Gate**: full

**Commit**: `feat(salas): create a room over the API`

---

### T16: List rooms over HTTP

**What**: Add `GET /api/rooms` so filters and a valid interval reach `ListRooms`, and a bad interval returns 422 with no availability flags.
**Where**: `app/Http/Controllers/RoomController.php`
**Depends on**: T15
**Reuses**: `ListRooms`, `IntervalValidator`
**Requirement**: SALA-05, SALA-18, SALA-19, SALA-20, SALA-23, SALA-24, SALA-26, SALA-28, SALA-29, SALA-30, SALA-31, SALA-32, SALA-33, SALA-34, SALA-35, SALA-36, SALA-37, SALA-38, SALA-39, SALA-40, SALA-41

**Done when**:

- [ ] A failed interval returns the spec sentence and does not include `disponivel` or `indisponivel`
- [ ] `RoomApiTest.php` also passes: `it lists rooms over HTTP`, `it filters rooms by minimum capacity`, `it filters rooms by resource ignoring case`, `it applies capacity and resource together`, `it returns every room when filters are blank`, `it rejects an invalid minimum capacity`, `it marks an overlapping active room indisponivel`, `it marks a free active room disponivel`, `it leaves an inactive room without availability`, `it treats a touching reservation as free`, `it ignores a reservation on another room`, `it ignores a reservation on another date`, `it rejects an availability query outside business hours`, `it rejects an availability query over 4 hours`, `it rejects an availability query whose end is not after its start`, `it rejects an availability query before today`, `it reports only the first failing availability check`, `it accepts a duration of exactly 4 hours`, `it accepts a start of 08:00`, `it accepts an end of 18:00`
- [ ] Test count: 20 new tests pass
- [ ] Gate check passes: `vendor/bin/pint --dirty --format agent; php artisan test tests/Feature/Salas --testdox`

**Tests**: feature
**Gate**: full

**Commit**: `feat(salas): list and filter rooms over the API`

---

### T17: Show one room over HTTP

**What**: Add `GET /api/rooms/{room}`.
**Where**: `app/Http/Controllers/RoomController.php`
**Depends on**: T16
**Reuses**: `RoomResource`, implicit binding
**Requirement**: SALA-11

**Done when**:

- [ ] HTTP 200 returns that room's name, capacity, resources, and active flag
- [ ] `RoomApiTest.php` passes `it shows one room`
- [ ] Test count: 1 new test passes
- [ ] Gate check passes: `vendor/bin/pint --dirty --format agent; php artisan test tests/Feature/Salas --testdox`

**Tests**: feature
**Gate**: full

**Commit**: `feat(salas): show one room over the API`

---

### T18: Say when a room is missing

**What**: Render a missing `Room` on `api/*` as HTTP 404 with `Sala não encontrada`.
**Where**: `bootstrap/app.php`
**Depends on**: T17
**Reuses**: The existing JSON exception render for `api/*`
**Requirement**: SALA-15, SALA-56

**Done when**:

- [ ] Unknown room id returns `{ "message": "Sala não encontrada" }` and HTTP 404
- [ ] `RoomApiTest.php` passes `it returns 404 when the room does not exist`
- [ ] Test count: 1 new test passes
- [ ] Gate check passes: `vendor/bin/pint --dirty --format agent; php artisan test tests/Feature/Salas --testdox`

**Tests**: feature
**Gate**: full

**Commit**: `feat(salas): return the missing-room message`

---

### T19: Update a room over HTTP

**What**: Add `PUT /api/rooms/{room}` and keep duplicate-tag behavior on edit.
**Where**: `app/Http/Controllers/RoomController.php`
**Depends on**: T18
**Reuses**: `UpdateRoomRequest`, `RoomResource`
**Requirement**: SALA-72, SALA-73

**Done when**:

- [ ] HTTP 200 persists name, capacity, resources, and active, and returns the room
- [ ] `RoomApiTest.php` passes `it updates a room`
- [ ] Test count: 1 new test passes
- [ ] Gate check passes: `vendor/bin/pint --dirty --format agent; php artisan test tests/Feature/Salas --testdox`

**Tests**: feature
**Gate**: full

**Commit**: `feat(salas): update a room over the API`

---

### T20: Activate a room

**What**: Add `POST /api/rooms/{room}/activate`.
**Where**: `app/Http/Controllers/ActivateRoomController.php`
**Depends on**: T19
**Reuses**: `routes/api.php`, `RoomResource`
**Requirement**: SALA-76

**Done when**:

- [ ] The room becomes active and HTTP 200 returns it
- [ ] `RoomApiTest.php` passes `it activates a room`
- [ ] Test count: 1 new test passes
- [ ] Gate check passes: `vendor/bin/pint --dirty --format agent; php artisan test tests/Feature/Salas --testdox`

**Tests**: feature
**Gate**: full

**Commit**: `feat(salas): activate a room`

---

### T21: Inactivate a room without deleting history

**What**: Add `POST /api/rooms/{room}/inactivate` and keep the row and its confirmed reservations.
**Where**: `app/Http/Controllers/InactivateRoomController.php`
**Depends on**: T20
**Reuses**: `routes/api.php`
**Requirement**: SALA-75, SALA-77, SALA-78

**Done when**:

- [ ] No request field is required to confirm the action
- [ ] `RoomApiTest.php` passes `it inactivates a room`, `it keeps confirmed reservations when a room is inactivated`, `it keeps the room row when a room is inactivated`
- [ ] Test count: 3 new tests pass
- [ ] Gate check passes: `vendor/bin/pint --dirty --format agent; php artisan test tests/Feature/Salas --testdox`

**Tests**: feature
**Gate**: full

**Commit**: `feat(salas): inactivate a room and keep its reservations`

---

### T22: Create a reservation over HTTP

**What**: Add `POST /api/reservations` with the spec status codes, and prove the salas routes accept an anonymous caller.
**Where**: `app/Http/Controllers/ReservationController.php`
**Depends on**: T21
**Reuses**: `CreateReservation`, `ReservationResource`, `routes/api.php`
**Requirement**: SALA-45, SALA-47, SALA-48, SALA-49, SALA-50, SALA-51, SALA-52, SALA-53, SALA-54, SALA-55, SALA-56, SALA-57, SALA-59, SALA-60, SALA-61, SALA-79, SALA-80

**Done when**:

- [ ] Success is HTTP 201. Conflict is HTTP 409 `Conflito de horário`. Inactive room and bad input are HTTP 422. Unknown room is HTTP 404. No credentials are required
- [ ] `tests/Feature/Salas/ReservationApiTest.php` passes: `it stores one confirmed reservation`, `it rejects an empty subject`, `it rejects a subject longer than 200 characters`, `it rejects a reservation for an inactive room`, `it rejects an overlapping reservation`, `it rejects a reservation whose end is not after its start`, `it rejects a reservation before today`, `it rejects a reservation outside business hours`, `it rejects a reservation over 4 hours`, `it uses the first failing check on create`, `it rechecks the conflict at create time`, `it confirms exactly one of two concurrent creates`, `it stores two reservations that only touch`, `it ignores an unconfirmed reservation`, `it serves salas actions with no credentials`
- [ ] Test count: 15 new tests pass
- [ ] Gate check passes: `vendor/bin/pint --dirty --format agent; php artisan test tests/Feature/Salas --testdox`

**Tests**: feature
**Gate**: full

**Commit**: `feat(salas): create a reservation over the API`

---

### T23: Share one JSON client

**What**: Add `api()` that returns JSON or throws `ApiError` with the server `message` and status.
**Where**: `resources/js/services/api.ts`
**Depends on**: T22
**Reuses**: The existing `getHealth` fetch headers
**Requirement**: SALA-08

**Done when**:

- [ ] Failed responses throw `ApiError` whose message is the JSON `message`
- [ ] `npm run typecheck` succeeds
- [ ] Gate check passes: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Tests**: none - React client, no component runner
**Gate**: build

**Commit**: `feat(salas): throw API errors with the server message`

---

### T24: Build the Salas list page

**What**: Replace the Salas placeholder with the room grid, filters, availability query, and list states.
**Where**: `resources/js/pages/SalasPage.tsx`
**Depends on**: T23
**Reuses**: `api()`, tokens from `AppLayout`
**Requirement**: SALA-01, SALA-02, SALA-03, SALA-04, SALA-06, SALA-07, SALA-08, SALA-09, SALA-10, SALA-21, SALA-22, SALA-23, SALA-24
**Capture**: `.specs/features/salas/captures/salas-list.html`

**Done when**:

- [ ] Heading is `Salas`. Each card matches the capture: name, `Capacidade: N pessoas`, one tag per resource, 3 columns above 768px, 1 column at 768px and below, plus `Ativa` or `Inativa`
- [ ] Controls are `Capacidade mínima`, `Recurso`, `Filtrar`, `Limpar`, `Consultar disponibilidade`, and `Nova sala`
- [ ] Empty, no-match, loading, and error copy match the spec, and `Tentar novamente` requests the list again
- [ ] A 422 from filter or availability clears `Disponível` and `Indisponível` and leaves the previous cards
- [ ] `npm run typecheck` succeeds

**Tests**: none - screen layer, proved by the walk and `captures/salas-list.running.png`
**Gate**: build

**Commit**: `feat(salas): show the room list`

---

### T25: Build the room detail page

**What**: Show one room and the activate, inactivate, edit, and reserve controls.
**Where**: `resources/js/pages/RoomDetailPage.tsx`
**Depends on**: T24
**Reuses**: `api()`, `SalasPage` tokens
**Requirement**: SALA-11, SALA-12, SALA-13, SALA-14, SALA-15, SALA-16, SALA-17, SALA-75, SALA-76

**Done when**:

- [ ] Active rooms show `Reservar` and `Editar`. Inactive rooms show `Inativa`, `Ativar`, and no `Reservar`
- [ ] `Inativar` sends the request immediately, with no confirmation dialog
- [ ] Loading, missing room, and retry copy match the spec
- [ ] `npm run typecheck` succeeds

**Tests**: none - screen layer
**Gate**: build

**Commit**: `feat(salas): show a room and its actions`

---

### T26: Build the room form

**What**: Add the create and edit form with the spec labels and the active default.
**Where**: `resources/js/pages/RoomFormPage.tsx`
**Depends on**: T25
**Reuses**: `api()`
**Requirement**: SALA-62, SALA-63, SALA-64, SALA-73

**Done when**:

- [ ] Create opens from `Nova sala` with `Ativa` selected
- [ ] Edit opens filled with the current name, capacity, resources, and active flag
- [ ] The page shows the server `message`, and a non-validation failure shows `Não foi possível salvar a sala`
- [ ] `npm run typecheck` succeeds

**Tests**: none - screen layer
**Gate**: build

**Commit**: `feat(salas): add the room form`

---

### T27: Build the reservation page

**What**: Add the page that reserves the selected room and stays put on success.
**Where**: `resources/js/pages/ReservationPage.tsx`
**Depends on**: T26
**Reuses**: `api()`, mockup field labels
**Requirement**: SALA-43, SALA-44, SALA-46, SALA-58

**Done when**:

- [ ] Fields are `Sala`, `Data`, `Hora Início`, `Hora Fim`, and `Assunto`, with the room from the route selected
- [ ] HTTP 201 shows `Reserva criada com sucesso!` on this page and does not navigate away
- [ ] Any other failure shows the server `message`, or `Não foi possível criar a reserva` when the body has none
- [ ] `npm run typecheck` succeeds

**Tests**: none - screen layer
**Gate**: build

**Commit**: `feat(salas): add the reservation page`

---

### T28: Register the Salas routes

**What**: Mount the list, create, detail, edit, and reserve pages under the existing shell.
**Where**: `resources/js/app.tsx`
**Depends on**: T27
**Reuses**: `Shell`, `NavTabs` (`/salas` already uses `end: false`)
**Requirement**: SALA-43, SALA-62

**Done when**:

- [ ] Routes are `/salas`, `/salas/nova`, `/salas/:id`, `/salas/:id/editar`, and `/salas/:id/reservar`
- [ ] They render inside `Shell`
- [ ] `npm run typecheck` and `npm run build` succeed
- [ ] Gate check passes: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Tests**: none - route wiring, typecheck and build
**Gate**: build

**Commit**: `feat(salas): register the room and reservation routes`

---

## Checks

**Build**: `npm run build; npm run typecheck; vendor/bin/pint --dirty --format agent; php artisan test`

**Run salas**: `php artisan test tests/Feature/Salas --testdox`

**C1** - Room cards match `captures/salas-list.html`: name, `Capacidade: N pessoas`, one tag per resource, 3 columns above 768px, 1 column at 768px and below (SALA-01, AC 1)
Proof: browser captures/salas-list.running.png

**C2** - The list heading is `Salas` (SALA-02, AC 2)
Proof: walk - open `/salas` and read the heading `Salas`

**C3** - An active room's card shows `Ativa` (SALA-03, AC 3)
Proof: walk - on `/salas`, an active room card shows `Ativa`

**C4** - An inactive room's card shows `Inativa` (SALA-04, AC 4)
Proof: walk - on `/salas`, an inactive room card shows `Inativa`

**C5** - The list includes every stored room (SALA-05, AC 5)
Proof: run salas | ✓ it lists rooms over HTTP

**C6** - An empty catalog shows `Nenhuma sala cadastrada` (SALA-06, AC 6)
Proof: walk - with no rooms, `/salas` shows `Nenhuma sala cadastrada`

**C7** - The list request shows `Carregando salas...` while it is in flight (SALA-07, AC 7)
Proof: walk - slow the list request and see `Carregando salas...`

**C8** - A failed list shows `Não foi possível carregar as salas` and `Tentar novamente` (SALA-08, AC 8)
Proof: walk - fail `GET /api/rooms` and see that sentence and `Tentar novamente`

**C9** - `Tentar novamente` requests the list again (SALA-09, AC 9)
Proof: walk - use `Tentar novamente` and see a new `GET /api/rooms`

**C10** - A long list scrolls with the page and keeps each card's name, capacity, and tags together (SALA-10, AC 10)
Proof: walk - with more cards than the viewport, scroll `/salas` and each card stays intact

**C11** - Opening a card shows name, capacity, resources, and `Ativa` or `Inativa` (SALA-11, AC 1)
Proof: run salas | ✓ it shows one room

**C12** - An active room shows `Reservar` and `Editar` (SALA-12, AC 2)
Proof: walk - open an active room and see `Reservar` and `Editar`

**C13** - An inactive room shows `Inativa` and no `Reservar` (SALA-13, AC 3)
Proof: walk - open an inactive room, see `Inativa`, and see no `Reservar`

**C14** - Detail shows `Carregando sala...` while the request is in flight (SALA-14, AC 4)
Proof: walk - slow the detail request and see `Carregando sala...`

**C15** - An unknown room shows `Sala não encontrada` (SALA-15, AC 5)
Proof: run salas | ✓ it returns 404 when the room does not exist

**C16** - A failed detail shows `Não foi possível carregar a sala` and `Tentar novamente` (SALA-16, AC 6)
Proof: walk - fail the detail request and see that sentence and `Tentar novamente`

**C17** - Detail retry requests that room again (SALA-17, AC 7)
Proof: walk - use `Tentar novamente` on the detail error and see a new detail request

**C18** - Minimum capacity keeps rooms with capacity greater than or equal to it (SALA-18, AC 1)
Proof: run salas | ✓ it filters rooms by minimum capacity

**C19** - Resource filter matches a tag after trim, ignoring case (SALA-19, AC 2)
Proof: run salas | ✓ it filters rooms by resource ignoring case

**C20** - Both filters apply together (SALA-20, AC 3)
Proof: run salas | ✓ it applies capacity and resource together

**C21** - No match shows `Nenhuma sala encontrada` (SALA-21, AC 4)
Proof: walk - filter to a capacity and resource that match nothing and see `Nenhuma sala encontrada`

**C22** - `Limpar` clears both filters and lists every room (SALA-22, AC 5)
Proof: walk - use `Limpar` and see every room with empty filter fields

**C23** - Blank filters list every room (SALA-23, AC 6)
Proof: run salas | ✓ it returns every room when filters are blank

**C24** - There is no name search and no location filter (SALA-24, AC 7)
Proof: walk - the Salas list has no name field and no location field

**C25** - An invalid minimum capacity shows `A capacidade deve ser um número inteiro de 1 a 999` and keeps the previous list (SALA-25, AC 8)
Proof: run salas | ✓ it rejects an invalid minimum capacity

**C26** - A valid interval shows `Disponível` or `Indisponível` on every active room (SALA-26, AC 1)
Proof: run salas | ✓ it marks a free active room disponivel

**C27** - Overlap is start before the confirmed end and end after the confirmed start, same room and date (SALA-27, AC 2)
Proof: run salas | ✓ it marks an overlapping active room indisponivel

**C28** - An overlapping confirmed reservation shows `Indisponível` (SALA-28, AC 3)
Proof: run salas | ✓ it marks an overlapping active room indisponivel

**C29** - An active room with no overlap shows `Disponível` (SALA-29, AC 4)
Proof: run salas | ✓ it marks a free active room disponivel

**C30** - An inactive room keeps `Inativa` and does not show `Disponível` or `Indisponível` (SALA-30, AC 5)
Proof: run salas | ✓ it leaves an inactive room without availability

**C31** - A reservation that only touches the query shows `Disponível` (SALA-31, AC 6)
Proof: run salas | ✓ it treats a touching reservation as free

**C32** - Another room's reservation does not block this room (SALA-32, AC 7)
Proof: run salas | ✓ it ignores a reservation on another room

**C33** - Another date's reservation does not block this date (SALA-33, AC 8)
Proof: run salas | ✓ it ignores a reservation on another date

**C34** - A query outside 08:00–18:00 shows `Reservas permitidas apenas entre 8h e 18h` and no availability marks (SALA-34, AC 9)
Proof: run salas | ✓ it rejects an availability query outside business hours

**C35** - A query over 4 hours shows `Duração máxima permitida é de 4 horas` and no availability marks (SALA-35, AC 10)
Proof: run salas | ✓ it rejects an availability query over 4 hours

**C36** - A query whose end is not after its start shows `Horário de fim deve ser posterior ao horário de início` and no availability marks (SALA-36, AC 11)
Proof: run salas | ✓ it rejects an availability query whose end is not after its start

**C37** - A query before today shows `A data não pode ser anterior a hoje` and no availability marks (SALA-37, AC 12)
Proof: run salas | ✓ it rejects an availability query before today

**C38** - Only the first failing interval check is reported, in the spec order (SALA-38, AC 13)
Proof: run salas | ✓ it reports only the first failing availability check

**C39** - A duration of exactly 4 hours is accepted (SALA-39, AC 14)
Proof: run salas | ✓ it accepts a duration of exactly 4 hours

**C40** - A start time of 08:00 is accepted (SALA-40, AC 15)
Proof: run salas | ✓ it accepts a start of 08:00

**C41** - An end time of 18:00 is accepted (SALA-41, AC 16)
Proof: run salas | ✓ it accepts an end of 18:00

**C42** - Create uses the same 08:00–18:00 window and 4-hour maximum (SALA-42, AC 17)
Proof: run salas | ✓ it rejects a reservation outside business hours

**C43** - `Reservar` on an active room opens the reservation page with that room selected (SALA-43, AC 1)
Proof: walk - use `Reservar` on an active room and land on its reservation page with that room selected

**C44** - The reservation page shows `Sala`, `Data`, `Hora Início`, `Hora Fim`, and `Assunto` (SALA-44, AC 2)
Proof: walk - the reservation page shows those five labels

**C45** - A valid reservation returns HTTP 201 and stores one confirmed row (SALA-45, AC 3)
Proof: run salas | ✓ it stores one confirmed reservation

**C46** - Success shows `Reserva criada com sucesso!` and stays on the reservation page (SALA-46, AC 4)
Proof: walk - after a successful reserve, that sentence is on the same reservation page

**C47** - An empty subject returns HTTP 422 `Informe o assunto` and stores nothing (SALA-47, AC 5)
Proof: run salas | ✓ it rejects an empty subject

**C48** - A subject over 200 characters returns HTTP 422 `O assunto deve ter no máximo 200 caracteres` and stores nothing (SALA-48, AC 6)
Proof: run salas | ✓ it rejects a subject longer than 200 characters

**C49** - An inactive room returns HTTP 422 `Sala inativa não permite nova reserva` and stores nothing (SALA-49, AC 7)
Proof: run salas | ✓ it rejects a reservation for an inactive room

**C50** - An overlap returns HTTP 409 `Conflito de horário` and stores nothing (SALA-50, AC 8)
Proof: run salas | ✓ it rejects an overlapping reservation

**C51** - Create with end not after start returns HTTP 422 `Horário de fim deve ser posterior ao horário de início` and stores nothing (SALA-51, AC 9)
Proof: run salas | ✓ it rejects a reservation whose end is not after its start

**C52** - Create before today returns HTTP 422 `A data não pode ser anterior a hoje` and stores nothing (SALA-52, AC 10)
Proof: run salas | ✓ it rejects a reservation before today

**C53** - Create outside business hours returns HTTP 422 `Reservas permitidas apenas entre 8h e 18h` and stores nothing (SALA-53, AC 11)
Proof: run salas | ✓ it rejects a reservation outside business hours

**C54** - Create over 4 hours returns HTTP 422 `Duração máxima permitida é de 4 horas` and stores nothing (SALA-54, AC 12)
Proof: run salas | ✓ it rejects a reservation over 4 hours

**C55** - Create uses the same first-failure order as the availability query (SALA-55, AC 13)
Proof: run salas | ✓ it uses the first failing check on create

**C56** - An unknown room on create returns HTTP 404 `Sala não encontrada` and stores nothing (SALA-56, AC 14)
Proof: run salas | ✓ it returns 404 when the room does not exist

**C57** - Create checks the conflict again after a query that showed `Disponível` (SALA-57, AC 15)
Proof: run salas | ✓ it rechecks the conflict at create time

**C58** - A non-validation create failure shows `Não foi possível criar a reserva` and stores nothing (SALA-58, AC 16)
Proof: walk - force a server failure on create and see `Não foi possível criar a reserva` with no new row

**C59** - Two concurrent overlapping creates leave one HTTP 201, one HTTP 409 `Conflito de horário`, and one confirmed row (SALA-59, AC 1)
Proof: run salas | ✓ it confirms exactly one of two concurrent creates

**C60** - Two creates that only touch both return HTTP 201 and store two confirmed rows (SALA-60, AC 2)
Proof: run salas | ✓ it stores two reservations that only touch

**C61** - An unconfirmed overlap does not reject the create and does not show `Indisponível` (SALA-61, AC 3)
Proof: run salas | ✓ it ignores an unconfirmed reservation

**C62** - `Nova sala` shows the room form (SALA-62, AC 1)
Proof: walk - use `Nova sala` and see the room form

**C63** - The form shows `Nome`, `Capacidade`, `Recursos`, and `Ativa`, with `Ativa` selected on create (SALA-63, AC 2)
Proof: walk - the create form shows those fields and `Ativa` is selected

**C64** - `Editar` shows the form filled with the current room (SALA-64, AC 3)
Proof: walk - use `Editar` and see the current name, capacity, resources, and active flag

**C65** - A valid create returns HTTP 201, stores the room, and shows its details (SALA-65, AC 4)
Proof: run salas | ✓ it creates a room

**C66** - An empty name returns HTTP 422 `Informe o nome da sala` and stores nothing (SALA-66, AC 5)
Proof: run salas | ✓ it rejects an empty room name

**C67** - A name over 100 characters returns HTTP 422 `O nome da sala deve ter no máximo 100 caracteres` and stores nothing (SALA-67, AC 6)
Proof: run salas | ✓ it rejects a room name over 100 characters

**C68** - A bad capacity returns HTTP 422 `A capacidade deve ser um número inteiro de 1 a 999` and stores nothing (SALA-68, AC 7)
Proof: run salas | ✓ it rejects a capacity outside 1 to 999

**C69** - Omitted resources store an empty list (SALA-69, AC 8)
Proof: run salas | ✓ it stores a room without resources

**C70** - A repeated resource is stored once, keeping the first spelling (SALA-70, AC 9)
Proof: run salas | ✓ it stores one tag for a repeated resource

**C71** - Two rooms may share a name (SALA-71, AC 10)
Proof: run salas | ✓ it allows two rooms to share a name

**C72** - A valid edit returns HTTP 200 and shows the updated details (SALA-72, AC 11)
Proof: run salas | ✓ it updates a room

**C73** - A non-validation save failure shows `Não foi possível salvar a sala` and does not claim success (SALA-73, AC 12)
Proof: walk - fail a save and see `Não foi possível salvar a sala`

**C74** - There is no control that deletes a room (SALA-74, AC 13)
Proof: run salas | ✓ it does not expose a room delete route

**C75** - `Inativar` marks the room inactive, shows `Inativa`, and does not ask for confirmation (SALA-75, AC 1)
Proof: run salas | ✓ it inactivates a room

**C76** - `Ativar` marks the room active and shows `Ativa` (SALA-76, AC 2)
Proof: run salas | ✓ it activates a room

**C77** - Inactivating a room keeps its confirmed reservations (SALA-77, AC 3)
Proof: run salas | ✓ it keeps confirmed reservations when a room is inactivated

**C78** - Inactivating a room does not remove the room row (SALA-78, AC 4)
Proof: run salas | ✓ it keeps the room row when a room is inactivated

**C79** - A new reservation for a room just inactivated returns HTTP 422 `Sala inativa não permite nova reserva` and stores nothing (SALA-79, AC 5)
Proof: run salas | ✓ it rejects a reservation for an inactive room

**C80** - List, show, create, update, activate, inactivate, availability, and reservation create work with no credentials (SALA-80, AC 1)
Proof: run salas | ✓ it serves salas actions with no credentials

---

## Handoff

**Settled mid-build:** none
**Dispatched:** batch worker phases 1–2 (T1–T10) — sent, not yet returned
**Walked:** none

---

## Phase Execution Map

```
Phase 1 → Phase 2 → Phase 3 → Phase 4 → Phase 5 → Phase 6

Phase 1:  T1 → T2 → T3 → T4 → T5 → T6
Phase 2:  T6 → T7 → T8 → T9 → T10
Phase 3:  T10 → T11 → T12 → T13
Phase 4:  T13 → T14 → T15 → T16 → T17 → T18 → T19 → T20 → T21
Phase 5:  T21 → T22
Phase 6:  T22 → T23 → T24 → T25 → T26 → T27 → T28
```

Execution is strictly sequential. One task at a time.

---

## Task Granularity Check

| Task | Scope | Status |
| ---- | ----- | ------ |
| T1–T10 | One config, migration, model, or factory file | Granular |
| T11–T13 | One domain class plus its feature tests | Granular |
| T14–T22 | One resource, controller action, or exception render | Granular |
| T23–T28 | One client file or one page | Granular |

## Diagram-Definition Cross-Check

Each task depends only on the previous task. The phase fences and the map above are that same chain, including the arrow from the last task of a phase to the first task of the next.

## Test Co-location Validation

| Task | Layer | Matrix | Task says | Status |
| ---- | ----- | ------ | --------- | ------ |
| T1, T2 | Config | none | none | OK |
| T3, T5, T7 | Schema | none | none | OK |
| T4, T6, T8 | Entity | none | none | OK |
| T9, T10 | Factory | none | none | OK |
| T11–T13 | Domain | feature | feature | OK |
| T14–T22 | HTTP | feature | feature | OK |
| T23–T28 | React | none | none | OK |
