# Salas Specification

**Approved**: 2026-11-06 - user: "Aprovado"

## Problem Statement

A visitor can open Salas and only sees a placeholder. HU-002 asks for the room catalog, availability, a reservation, and room administration, so the visitor can pick a room and book it without a conflicting reservation.

## Goals

- [ ] The Salas list shows every room, active and inactive, with the mockup card composition
- [ ] The visitor can filter by minimum capacity and resource, and can query a free interval
- [ ] The visitor can create one confirmed reservation on its own page, with conflict checked again on the server
- [ ] Any visitor can create, edit, activate, and deactivate rooms without logging in

## Out of Scope

| Feature | Reason |
| ------- | ------ |
| Login, sessions, and an admin role | AD-001 and AD-006 |
| Recurring reservations | HU-002 out of scope |
| External calendar integration | HU-002 out of scope |
| Automatic notifications | HU-002 out of scope |
| Maintenance blocks | HU-002 out of scope; user chose no maintenance block (AD-007) |
| Floor plans and image upload | HU-002 out of scope; user chose no image |
| Location field and location filter | User chose no location |
| Search by room name | Plan default: search is the two filters only |
| Limit of 3 active reservations | AD-010 |
| Cancelling or editing a reservation | Not in HU-002; Minhas Reservas is a later feature |
| Dashboard body and Minhas Reservas body | Later features; AD-005 still holds for those routes |
| Hard delete of a room | HU-002 only activates and inactivates |

---

## Assumptions & Open Questions

| Assumption / decision | Chosen default | Rationale | Confirmed? |
| --------------------- | -------------- | --------- | ---------- |
| Room attributes | Name required, capacity required, resources optional, active defaults to true | User chose the mockup set. No location and no image | y |
| Inactive rooms on the list | Shown, labeled `Inativa`, and not open for a new reservation | User option on who sees an inactive room (AD-008) | y |
| Filters | Minimum capacity and resource only | User option (AD-009) | y |
| Availability shape | Free interval: date, start, end. Window 08:00–18:00. Maximum 4 hours | User option (AD-009). Messages copied from the mockup | y |
| Reservation UI | Own page, room preselected. Success stays on that page | User option (AD-007). Plan default for the success state | y |
| Who can manage rooms | Any visitor, no credential | User option (AD-006). AD-001 stays | y |
| List heading | `Salas` | `Salas Disponíveis` would contradict listing inactive rooms (AD-008) | y |
| Today | The calendar date in `config('app.timezone')`, currently `UTC` | The app timezone is UTC. The spec does not invent a second clock | y |
| Touching intervals | 10:00–11:00 and 11:00–12:00 do not overlap | Overlap is start < other end and end > other start | y |
| Name bounds | 1 to 100 characters after trim | Plan default, so the name rule is testable | y |
| Capacity bounds | Integer from 1 to 999 | Plan default, so the capacity rule is testable | y |
| Subject bounds | Required, 1 to 200 characters after trim | The mockup form requires Assunto. The bound makes the rule testable | y |
| Duplicate resources | Stored once, case-insensitive after trim, first spelling kept | Plan default | y |
| Duplicate room names | Allowed | HU-002 does not require unique names | y |
| Validation HTTP status | HTTP 422, and no row written | One code for rejected input, distinct from conflict | y |
| Conflict HTTP status | HTTP 409 and `Conflito de horário` | Plan requirement for the losing create | y |
| Unknown room | HTTP 404 and `Sala não encontrada` | A missing id is not a validation error on a field | y |
| Create and update codes | Room create and reservation create return HTTP 201. Room update returns HTTP 200 | Distinguishes create from update | y |
| Inactivate confirmation | No dialog. `Inativar` applies immediately | HU-002 does not ask for a confirm step | y |
| Filter and form labels | `Capacidade mínima`, `Recurso`, `Filtrar`, `Limpar`, `Consultar disponibilidade`, `Nova sala`, `Editar`, `Reservar`, `Ativar`, `Inativar` | Each control needs one visible label | y |
| Interval check order | End not after start, then date before today, then outside 08:00–18:00, then duration over 4 hours | One message when several checks fail | y |
| Non-confirmed reservations | Ignored for availability and conflict | Only a confirmed reservation blocks the interval. Cancel stays out of scope | y |
| Rate limit | N/A because the HU has no throttle | Academic demo | y |
| Idempotency key | N/A because a second overlapping create is a conflict, not a replay | CA07 | y |
| Retention and expiry | N/A because the HU has no TTL. Inactivate keeps the row and its confirmed reservations | Data lifecycle in this feature is activate and inactivate | y |
| Metrics and tracing | N/A because the HU asks for no telemetry | Academic demo | y |
| External dependency failure | N/A because room and reservation data stay in this application | No calendar or notification provider | y |
| Unauthorized screen | N/A because there is no login and no admin role | AD-001 and AD-006 | y |

**Open questions:** none - all resolved or logged above.

---

## User Stories

### P1: Room list ⭐ MVP

**User Story**: As a visitor, I want to see every room and whether it is active so that I can tell which spaces exist before I book one.

**Why P1**: HU-002 CA01 and CA02 start from the list. The current `/salas` route is a placeholder.

**Acceptance Criteria**:
1. WHEN a visitor opens the Salas list THEN the system SHALL show each room as a card composed as in `captures/salas-list.html`: the room name, the line `Capacidade: N pessoas` where N is that room's capacity, one feature tag per stored resource, a 3-column grid when the viewport is wider than 768px, and a single column when the viewport is 768px or narrower
2. WHEN a visitor opens the Salas list THEN the system SHALL show the heading `Salas`
3. WHEN a room is active THEN the system SHALL show `Ativa` on that room's card
4. WHEN a room is inactive THEN the system SHALL show `Inativa` on that room's card
5. The system SHALL include every stored room, active and inactive, in the Salas list
6. WHEN no room is stored THEN the system SHALL show `Nenhuma sala cadastrada`
7. WHILE the room list request is in flight the system SHALL show `Carregando salas...`
8. IF the room list request fails THEN the system SHALL show `Não foi possível carregar as salas` and a `Tentar novamente` control
9. WHEN a visitor uses `Tentar novamente` after a failed list THEN the system SHALL request the room list again
10. WHEN the room list is taller than the viewport THEN the system SHALL scroll the cards with the page and SHALL keep each card's name, capacity line, and resource tags on that card

**Independent Test**: With one active room and one inactive room, open Salas and see both cards, the heading `Salas`, and the situation labels. With no rooms, see `Nenhuma sala cadastrada`.

---

### P1: Room details ⭐ MVP

**User Story**: As a visitor, I want to open a room and read its data and situation so that I know whether I can reserve it.

**Why P1**: HU-002 CA02. The mockup card click goes straight to a modal. This feature opens the detail first (AD-007).

**Acceptance Criteria**:
1. WHEN a visitor activates a room card THEN the system SHALL show that room's name, capacity, each resource, and situation `Ativa` or `Inativa`
2. WHEN the opened room is active THEN the system SHALL show a `Reservar` control and an `Editar` control
3. WHEN the opened room is inactive THEN the system SHALL show `Inativa` and SHALL NOT show a `Reservar` control
4. WHILE the room detail request is in flight the system SHALL show `Carregando sala...`
5. IF the room does not exist THEN the system SHALL show `Sala não encontrada`
6. IF the room detail request fails for a reason other than an unknown room THEN the system SHALL show `Não foi possível carregar a sala` and a `Tentar novamente` control
7. WHEN a visitor uses `Tentar novamente` after a failed detail request THEN the system SHALL request that room again

**Independent Test**: Open an active room and see its fields plus `Reservar`. Open an inactive room and see `Inativa` without `Reservar`.

---

### P1: Filter rooms ⭐ MVP

**User Story**: As a visitor, I want to filter rooms by minimum capacity and by resource so that I only look at spaces that fit the meeting.

**Why P1**: HU-002 asks for search and filter. The user fixed the criteria as capacity and resource (AD-009).

**Acceptance Criteria**:
1. WHEN a visitor enters a minimum capacity from 1 to 999 and uses `Filtrar` THEN the system SHALL list only rooms whose capacity is greater than or equal to that number
2. WHEN a visitor enters a resource and uses `Filtrar` THEN the system SHALL list only rooms that have a resource equal to that text after trimming, ignoring case
3. WHEN a visitor enters both a minimum capacity and a resource and uses `Filtrar` THEN the system SHALL list only rooms that satisfy both conditions
4. WHEN the applied filters match no room THEN the system SHALL show `Nenhuma sala encontrada`
5. WHEN a visitor uses `Limpar` THEN the system SHALL clear both filters and list every stored room
6. WHEN both filters are blank and the visitor uses `Filtrar` THEN the system SHALL list every stored room
7. The system SHALL NOT provide a name search or a location filter on the Salas list
8. IF the minimum capacity is not an integer from 1 to 999 WHEN the visitor uses `Filtrar` THEN the system SHALL show `A capacidade deve ser um número inteiro de 1 a 999` and SHALL keep the list that was showing before that attempt

**Independent Test**: Filter a set of rooms by capacity 10 and resource `projetor`, then clear the filters and see the full list again.

---

### P1: Availability query ⭐ MVP

**User Story**: As a visitor, I want to ask whether each room is free for a date and a time range so that I can choose a room before I reserve it.

**Why P1**: HU-002 CA03, CA04, and CA05. The query is informational. Create checks the conflict again.

**Acceptance Criteria**:
1. WHEN a visitor submits a date, a start time, and an end time that pass the interval rules and uses `Consultar disponibilidade` THEN the system SHALL show exactly one of `Disponível` or `Indisponível` on every active room in the current list
2. The system SHALL treat two intervals on the same room and the same date as overlapping only when the queried start is before the confirmed reservation end and the queried end is after the confirmed reservation start
3. WHEN an active room has a confirmed reservation that overlaps the query THEN the system SHALL show `Indisponível` on that room
4. WHEN an active room has no overlapping confirmed reservation THEN the system SHALL show `Disponível` on that room
5. WHEN a room is inactive THEN the system SHALL keep the label `Inativa` on it and SHALL NOT show `Disponível` or `Indisponível` on it
6. WHEN an active room's only confirmed reservation on that date ends at the queried start minute, or starts at the queried end minute THEN the system SHALL show `Disponível` on that room
7. WHEN the only overlapping confirmed reservation belongs to another room THEN the system SHALL show `Disponível` on the queried active room
8. WHEN the only overlapping confirmed reservation for that room is on another date THEN the system SHALL show `Disponível` on the queried active room
9. IF the start time is before 08:00 or the end time is after 18:00 THEN the system SHALL show `Reservas permitidas apenas entre 8h e 18h` and SHALL NOT show `Disponível` or `Indisponível` on any room
10. IF the duration is greater than 4 hours THEN the system SHALL show `Duração máxima permitida é de 4 horas` and SHALL NOT show `Disponível` or `Indisponível` on any room
11. IF the end time is not after the start time THEN the system SHALL show `Horário de fim deve ser posterior ao horário de início` and SHALL NOT show `Disponível` or `Indisponível` on any room
12. IF the date is before today in the application timezone THEN the system SHALL show `A data não pode ser anterior a hoje` and SHALL NOT show `Disponível` or `Indisponível` on any room
13. The system SHALL show only the first failing interval check, in this order: end time not after start time, date before today, start before 08:00 or end after 18:00, duration greater than 4 hours
14. The system SHALL accept a duration of exactly 4 hours when the rest of the interval passes the checks
15. The system SHALL accept a start time of 08:00 when the rest of the interval passes the checks
16. The system SHALL accept an end time of 18:00 when the rest of the interval passes the checks
17. The system SHALL apply this same 08:00–18:00 window and this same 4-hour maximum when a reservation is created

**Independent Test**: Query today 09:00–10:00 against a room that already has a confirmed 09:30–10:30 reservation and see `Indisponível`. Query 10:00–11:00 against a reservation that ends at 10:00 and see `Disponível`.

---

### P1: Reservation page ⭐ MVP

**User Story**: As a visitor, I want to reserve an active room on its own page so that the booking is stored only when the interval is still free.

**Why P1**: HU-002 asks for an action that starts a reservation, and CA03–CA07 include the create rules. The user replaced the mockup modal with a page (AD-007).

**Acceptance Criteria**:
1. WHEN a visitor uses `Reservar` on an active room THEN the system SHALL open a reservation page with that room already selected
2. The reservation page SHALL show the fields `Sala`, `Data`, `Hora Início`, `Hora Fim`, and `Assunto`
3. WHEN a visitor submits a valid reservation for an active room THEN the system SHALL respond with HTTP 201 and store exactly one confirmed reservation for that room, date, start, end, and subject
4. WHEN that create succeeds THEN the system SHALL show `Reserva criada com sucesso!` on the reservation page and SHALL remain on that page
5. IF the subject is empty or only whitespace THEN the system SHALL respond with HTTP 422, show `Informe o assunto`, and store no reservation
6. IF the subject is longer than 200 characters after trim THEN the system SHALL respond with HTTP 422, show `O assunto deve ter no máximo 200 caracteres`, and store no reservation
7. IF the selected room is inactive THEN the system SHALL respond with HTTP 422, show `Sala inativa não permite nova reserva`, and store no reservation
8. IF a confirmed reservation overlaps the submitted interval on that room and date THEN the system SHALL respond with HTTP 409, show `Conflito de horário`, and store no reservation
9. IF the end time is not after the start time WHEN the visitor submits a reservation THEN the system SHALL respond with HTTP 422, show `Horário de fim deve ser posterior ao horário de início`, and store no reservation
10. IF the date is before today in the application timezone WHEN the visitor submits a reservation THEN the system SHALL respond with HTTP 422, show `A data não pode ser anterior a hoje`, and store no reservation
11. IF the start time is before 08:00 or the end time is after 18:00 WHEN the visitor submits a reservation THEN the system SHALL respond with HTTP 422, show `Reservas permitidas apenas entre 8h e 18h`, and store no reservation
12. IF the duration is greater than 4 hours WHEN the visitor submits a reservation THEN the system SHALL respond with HTTP 422, show `Duração máxima permitida é de 4 horas`, and store no reservation
13. The system SHALL use the same first-failure order on reservation create as on the availability query
14. IF the selected room does not exist THEN the system SHALL respond with HTTP 404, show `Sala não encontrada`, and store no reservation
15. The system SHALL check the conflict again on the server at create time, including when an earlier query showed `Disponível`
16. IF creating the reservation fails for a reason other than validation, an unknown room, or a conflict THEN the system SHALL show `Não foi possível criar a reserva` and store no reservation

**Independent Test**: Reserve an active room for a free interval inside 08:00–18:00 and see the success message on the same page. Submit an overlapping interval and see `Conflito de horário` with no second row.

---

### P1: Concurrent reservation ⭐ MVP

**User Story**: As a visitor, I want two simultaneous bookings of the same interval to produce a single confirmed reservation so that the room is not double-booked.

**Why P1**: HU-002 CA07. The create path must recheck under concurrency, not only the earlier query.

**Acceptance Criteria**:
1. WHEN two reservation creates for the same room, date, and overlapping interval are processed concurrently THEN the system SHALL accept exactly one with HTTP 201, reject the other with HTTP 409 and `Conflito de horário`, and store exactly one confirmed reservation
2. WHEN two reservation creates for the same room and date only meet at an endpoint THEN the system SHALL accept both with HTTP 201 and store two confirmed reservations
3. WHEN a stored reservation for that room and date is not confirmed and overlaps the submitted interval THEN the system SHALL NOT reject the create for that overlap and SHALL NOT show `Indisponível` for that overlap

**Independent Test**: Fire two overlapping creates together and observe one HTTP 201, one HTTP 409, and one stored row.

---

### P1: Register and edit rooms ⭐ MVP

**User Story**: As a visitor, I want to register a room and correct its data so that the catalog matches the spaces we actually have.

**Why P1**: The user included room administration from the PDF in this feature (AD-006).

**Acceptance Criteria**:
1. WHEN a visitor uses `Nova sala` THEN the system SHALL show the room form
2. The room form SHALL include `Nome`, `Capacidade`, `Recursos`, and `Ativa`, and on create `Ativa` SHALL be selected
3. WHEN a visitor uses `Editar` on a room THEN the system SHALL show the room form filled with that room's current name, capacity, resources, and active flag
4. WHEN a visitor submits a create with a name of 1 to 100 characters after trim and a capacity integer from 1 to 999 THEN the system SHALL respond with HTTP 201, store the room, and show that room's details
5. IF the name is empty or only whitespace THEN the system SHALL respond with HTTP 422, show `Informe o nome da sala`, and store no room
6. IF the name is longer than 100 characters after trim THEN the system SHALL respond with HTTP 422, show `O nome da sala deve ter no máximo 100 caracteres`, and store no room
7. IF the capacity is not an integer from 1 to 999 THEN the system SHALL respond with HTTP 422, show `A capacidade deve ser um número inteiro de 1 a 999`, and store no room
8. WHEN the visitor submits a valid room and omits resources THEN the system SHALL store that room with an empty resource list
9. WHEN the visitor submits the same resource more than once on one room, ignoring case after trim THEN the system SHALL store that resource once and keep the first spelling
10. The system SHALL allow two stored rooms to have the same name
11. WHEN a visitor saves an edit with a valid name and a valid capacity THEN the system SHALL respond with HTTP 200, persist the name, capacity, resources, and active flag, and show the updated details
12. IF saving a room fails for a reason other than validation THEN the system SHALL show `Não foi possível salvar a sala` and SHALL NOT present the room as saved
13. The system SHALL NOT offer a control that deletes a room record

**Independent Test**: Create a room with two spellings of the same resource, see one tag on the detail, then edit the capacity and see the new value.

---

### P1: Activate and inactivate ⭐ MVP

**User Story**: As a visitor, I want to inactivate a room and later activate it again so that a closed space cannot take a new reservation.

**Why P1**: HU-002 CA06 and the administration scope the user included (AD-006, AD-008).

**Acceptance Criteria**:
1. WHEN a visitor uses `Inativar` on an active room THEN the system SHALL mark that room inactive, show `Inativa`, and SHALL NOT ask for confirmation
2. WHEN a visitor uses `Ativar` on an inactive room THEN the system SHALL mark that room active and show `Ativa`
3. WHEN a room is inactivated THEN the system SHALL keep that room's confirmed reservations stored
4. The system SHALL NOT remove the room record when the visitor inactivates it
5. IF a visitor submits a new reservation for a room that was just inactivated THEN the system SHALL respond with HTTP 422, show `Sala inativa não permite nova reserva`, and store no reservation

**Independent Test**: Inactivate a room that already has a confirmed reservation, see the reservation still stored, and see a new reservation refused.

---

### P1: Public access ⭐ MVP

**User Story**: As a visitor, I want every Salas action to work without signing in so that the academic demo matches the decision to skip authentication.

**Why P1**: AD-001 forbids login. AD-006 gives room administration to every visitor. This departs from HU-002 CA01 and CA08.

**Acceptance Criteria**:
1. The system SHALL list rooms, show a room, create a room, update a room, activate a room, inactivate a room, query availability, and create a reservation when the caller sends no credentials and no admin role

**Independent Test**: Call each of those actions with an empty cookie jar and no token. Valid requests succeed. Invalid requests return the validation or conflict response, not an authentication challenge.

---

## Edge Cases

- IF the submitted interval fails more than one check THEN the system SHALL show only the first message in the order fixed above
- WHEN a confirmed reservation ends at 11:00 and the next request starts at 11:00 on the same room and date THEN the system SHALL treat the intervals as free of conflict
- IF the visitor inactivates a room THEN the system SHALL keep the room row and its confirmed reservations
- WHEN the viewport is 768px wide THEN the system SHALL show one room card per row, as in `captures/salas-list.html`
- IF the caller is anonymous THEN the system SHALL NOT respond with an authentication challenge on the Salas actions

---

## Requirement Traceability

IDs follow story order, then criterion order inside the story.

| Requirement ID | Story | Phase | Status |
| -------------- | ----- | ----- | ------ |
| SALA-01 | P1: Room list | Specify | Implementing |
| SALA-02 | P1: Room list | Specify | Implementing |
| SALA-03 | P1: Room list | Specify | Implementing |
| SALA-04 | P1: Room list | Specify | Implementing |
| SALA-05 | P1: Room list | Specify | Implementing |
| SALA-06 | P1: Room list | Specify | Implementing |
| SALA-07 | P1: Room list | Specify | Implementing |
| SALA-08 | P1: Room list | Specify | Implementing |
| SALA-09 | P1: Room list | Specify | Implementing |
| SALA-10 | P1: Room list | Specify | Implementing |
| SALA-11 | P1: Room details | Specify | Implementing |
| SALA-12 | P1: Room details | Specify | Implementing |
| SALA-13 | P1: Room details | Specify | Implementing |
| SALA-14 | P1: Room details | Specify | Implementing |
| SALA-15 | P1: Room details | Specify | Implementing |
| SALA-16 | P1: Room details | Specify | Implementing |
| SALA-17 | P1: Room details | Specify | Implementing |
| SALA-18 | P1: Filter rooms | Specify | Implementing |
| SALA-19 | P1: Filter rooms | Specify | Implementing |
| SALA-20 | P1: Filter rooms | Specify | Implementing |
| SALA-21 | P1: Filter rooms | Specify | Implementing |
| SALA-22 | P1: Filter rooms | Specify | Implementing |
| SALA-23 | P1: Filter rooms | Specify | Implementing |
| SALA-24 | P1: Filter rooms | Specify | Implementing |
| SALA-25 | P1: Filter rooms | Specify | Implementing |
| SALA-26 | P1: Availability query | Specify | Implementing |
| SALA-27 | P1: Availability query | Specify | Pending |
| SALA-28 | P1: Availability query | Specify | Implementing |
| SALA-29 | P1: Availability query | Specify | Implementing |
| SALA-30 | P1: Availability query | Specify | Implementing |
| SALA-31 | P1: Availability query | Specify | Implementing |
| SALA-32 | P1: Availability query | Specify | Implementing |
| SALA-33 | P1: Availability query | Specify | Implementing |
| SALA-34 | P1: Availability query | Specify | Implementing |
| SALA-35 | P1: Availability query | Specify | Implementing |
| SALA-36 | P1: Availability query | Specify | Implementing |
| SALA-37 | P1: Availability query | Specify | Implementing |
| SALA-38 | P1: Availability query | Specify | Implementing |
| SALA-39 | P1: Availability query | Specify | Implementing |
| SALA-40 | P1: Availability query | Specify | Implementing |
| SALA-41 | P1: Availability query | Specify | Implementing |
| SALA-42 | P1: Availability query | Specify | Implementing |
| SALA-43 | P1: Reservation page | Specify | Implementing |
| SALA-44 | P1: Reservation page | Specify | Implementing |
| SALA-45 | P1: Reservation page | Specify | Implementing |
| SALA-46 | P1: Reservation page | Specify | Implementing |
| SALA-47 | P1: Reservation page | Specify | Implementing |
| SALA-48 | P1: Reservation page | Specify | Implementing |
| SALA-49 | P1: Reservation page | Specify | Implementing |
| SALA-50 | P1: Reservation page | Specify | Implementing |
| SALA-51 | P1: Reservation page | Specify | Implementing |
| SALA-52 | P1: Reservation page | Specify | Implementing |
| SALA-53 | P1: Reservation page | Specify | Implementing |
| SALA-54 | P1: Reservation page | Specify | Implementing |
| SALA-55 | P1: Reservation page | Specify | Implementing |
| SALA-56 | P1: Reservation page | Specify | Implementing |
| SALA-57 | P1: Reservation page | Specify | Implementing |
| SALA-58 | P1: Reservation page | Specify | Implementing |
| SALA-59 | P1: Concurrent reservation | Specify | Implementing |
| SALA-60 | P1: Concurrent reservation | Specify | Implementing |
| SALA-61 | P1: Concurrent reservation | Specify | Implementing |
| SALA-62 | P1: Register and edit rooms | Specify | Implementing |
| SALA-63 | P1: Register and edit rooms | Specify | Implementing |
| SALA-64 | P1: Register and edit rooms | Specify | Implementing |
| SALA-65 | P1: Register and edit rooms | Specify | Implementing |
| SALA-66 | P1: Register and edit rooms | Specify | Implementing |
| SALA-67 | P1: Register and edit rooms | Specify | Implementing |
| SALA-68 | P1: Register and edit rooms | Specify | Implementing |
| SALA-69 | P1: Register and edit rooms | Specify | Implementing |
| SALA-70 | P1: Register and edit rooms | Specify | Implementing |
| SALA-71 | P1: Register and edit rooms | Specify | Implementing |
| SALA-72 | P1: Register and edit rooms | Specify | Implementing |
| SALA-73 | P1: Register and edit rooms | Specify | Implementing |
| SALA-74 | P1: Register and edit rooms | Specify | Implementing |
| SALA-75 | P1: Activate and inactivate | Specify | Implementing |
| SALA-76 | P1: Activate and inactivate | Specify | Implementing |
| SALA-77 | P1: Activate and inactivate | Specify | Implementing |
| SALA-78 | P1: Activate and inactivate | Specify | Implementing |
| SALA-79 | P1: Activate and inactivate | Specify | Implementing |
| SALA-80 | P1: Public access | Specify | Implementing |

**ID format:** `SALA-[NUMBER]`

**Status values:** Pending → In Design → In Tasks → Implementing → Verified

**Coverage:** 80 total, 0 mapped to tasks, 80 unmapped

---

## Differences from the source

| Source shows | Product shows | Confirmed |
| ------------ | ------------- | --------- |
| Heading `Salas Disponíveis` | Heading `Salas` | y - AD-008 |
| Room cards with no situation label | Each card also shows `Ativa` or `Inativa` | y - AD-008 |
| Clicking a card opens the Nova Reserva modal | The card opens the room detail. `Reservar` opens a reservation page | y - AD-007 |
| No capacity or resource filters | Filters `Capacidade mínima` and `Recurso` | y - AD-009 |
| No availability query on the list | Date, start, and end mark each active room `Disponível` or `Indisponível` | y - AD-009 |
| Success closes the modal and reloads the dashboard | The reservation page shows `Reserva criada com sucesso!` and stays there | y - AD-007 |
| Alert `Limite de 3 reservas ativas excedido` | No limit of 3 reservations | y - AD-010 |
| No room create, edit, activate, or inactivate controls | Any visitor can do those actions | y - AD-006 |
| HU text: authenticated user, admin-only room changes (CA01, CA08) | No login. Every visitor lists rooms and manages them | y - AD-001 |

## Sources

- `.specs/hu/HU-002-Salas.pdf` - binding - room consultation, availability, reservation rules, and room administration for HU-002
- User options on 2026-11-06: full PDF without login (AD-006), reservation page (AD-007), inactive rooms visible (AD-008), capacity and resource filters with a free interval (AD-009)
- Plan defaults confirmed with "Implement the plan as specified": no 3-reservation limit (AD-010), heading `Salas`, touching intervals, name and capacity bounds
- ![salas-list](captures/salas-list.html) - binding - room card grid from the mockup: name, `Capacidade: N pessoas`, feature tags, 3 columns, one column at 768px and below. The heading inside the file is the mockup heading, not the product heading

---

## Success Criteria

- [ ] A visitor sees active and inactive rooms on Salas, with situation labels, and can open a room
- [ ] Minimum capacity and resource filters narrow the list, and a valid interval marks active rooms `Disponível` or `Indisponível`
- [ ] A valid reservation is stored once, an overlap returns HTTP 409 with `Conflito de horário`, and two concurrent overlaps leave one confirmed row
- [ ] A visitor creates, edits, activates, and inactivates a room with no credentials, and an inactive room refuses a new reservation
