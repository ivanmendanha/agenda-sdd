# Salas verification

**Verdict**: FAIL
**Proofs**: `proofs.md` at 562521a (verified tree at 8f05c43)
**Verifier**: independent sub-agent (author != verifier)

## Checks

| Check | Test | Holds |
| --- | --- | --- |
| C1 | `browser captures/salas-list.running.png` | not judged - capture row, `screens.md` is separate |
| C2 | walk - `/salas` heading `Salas` | yes - the Walked line reads the heading on the 1280×900 window |
| C3 | walk - active card shows `Ativa` | yes - the Walked line reads `Ativa` on the seeded active cards |
| C4 | walk - inactive card shows `Inativa` | yes - the Walked line reads `Inativa` on Sala 301 |
| C5 | `RoomApiTest.php` `it lists rooms over HTTP` | yes - asserts both the active and the inactive room in `data` |
| C6 | walk - no rooms shows `Nenhuma sala cadastrada` | no - the walk seeded 5 rooms and its own note says the empty state was not clicked |
| C7 | walk - `Carregando salas...` in flight | no - the Walked line says loading states were not clicked |
| C8 | walk - failed list shows the sentence and `Tentar novamente` | no - the Walked line says error states were not clicked |
| C9 | walk - `Tentar novamente` requests the list again | no - the retry was never used in the walk |
| C10 | walk - a list taller than the viewport scrolls with each card intact | no - the walk records 5 cards in a 3-column 1280×900 window, no scrolled list |
| C11 | `RoomApiTest.php` `it shows one room` | yes - asserts name, capacity, resources, and `active` |
| C12 | walk - active room shows `Reservar` and `Editar` | yes - `/salas/1` shows `Reservar`, `Editar`, `Inativar` |
| C13 | walk - inactive room shows `Inativa` and no `Reservar` | yes - `/salas/5` shows `Inativa`, `Ativar`, and no `Reservar` |
| C14 | walk - `Carregando sala...` in flight | no - the Walked line says loading states were not clicked |
| C15 | `RoomApiTest.php` `it returns 404 when the room does not exist` | yes - asserts 404 and exactly `Sala não encontrada` on `GET /api/rooms/404` |
| C16 | walk - failed detail shows the sentence and `Tentar novamente` | no - the Walked line says error states were not clicked |
| C17 | walk - detail retry requests that room again | no - the retry was never used in the walk |
| C18 | `RoomApiTest.php` `it filters rooms by minimum capacity` | yes - 9 is dropped, 10 and 11 are kept, so the bound is `>=` |
| C19 | `RoomApiTest.php` `it filters rooms by resource ignoring case` | yes - queries `' PROJETOR '` and keeps only the projector room |
| C20 | `RoomApiTest.php` `it applies capacity and resource together` | yes - rejects the too-small room and the wrong-resource room |
| C21 | walk - a filter that matches nothing shows `Nenhuma sala encontrada` | no - the Walked line says the empty-filter state was not clicked |
| C22 | walk - `Limpar` clears both filters and lists every room | no - the walk saw the control but never used it, so the clearing behavior is unproven |
| C23 | `RoomApiTest.php` `it returns every room when filters are blank` | yes - blank `min_capacity` and `resource` return both rooms |
| C24 | walk - no name field and no location field | yes - the Walked line enumerates the controls on `/salas` and neither appears |
| C25 | `RoomApiTest.php` `it rejects an invalid minimum capacity` | yes - asserts 422 and the sentence; "keeps the list that was showing" is UI and has no proof |
| C26 | `RoomApiTest.php` `it marks a free active room disponivel` | yes at HTTP - one active room marked; the rendered `Disponível` copy has no proof |
| C27 | `RoomApiTest.php` `it marks an overlapping active room indisponivel` | no - same test as C28; one interior overlap, the strict `<`/`>` bounds the criterion fixes are pinned only by C31's test |
| C28 | `RoomApiTest.php` `it marks an overlapping active room indisponivel` | yes - 09:00–10:00 against a confirmed 09:30–10:30 is `indisponivel` |
| C29 | `RoomApiTest.php` `it marks a free active room disponivel` | yes - a room with no reservation is `disponivel` |
| C30 | `RoomApiTest.php` `it leaves an inactive room without availability` | yes - asserts `active` false and `availability` null |
| C31 | `RoomApiTest.php` `it treats a touching reservation as free` | yes - reservations ending at the start and starting at the end both leave `disponivel` |
| C32 | `RoomApiTest.php` `it ignores a reservation on another room` | yes - asserts `disponivel` here and `indisponivel` on the other room |
| C33 | `RoomApiTest.php` `it ignores a reservation on another date` | yes - a 03-11 reservation leaves the 03-10 query `disponivel` |
| C34 | `RoomApiTest.php` `it rejects an availability query outside business hours` | yes - 422, the sentence, and no `disponivel` in the body |
| C35 | `RoomApiTest.php` `it rejects an availability query over 4 hours` | yes - 13:01 trips the maximum by one minute |
| C36 | `RoomApiTest.php` `it rejects an availability query whose end is not after its start` | yes - equal times return the order sentence |
| C37 | `RoomApiTest.php` `it rejects an availability query before today` | yes - 03-09 under a 03-10 clock |
| C38 | `RoomApiTest.php` `it reports only the first failing availability check` | yes - three stacked-failure requests pin all four steps of the order |
| C39 | `RoomApiTest.php` `it accepts a duration of exactly 4 hours` | yes - 09:00–13:00 returns 200 and `disponivel` |
| C40 | `RoomApiTest.php` `it accepts a start of 08:00` | yes |
| C41 | `RoomApiTest.php` `it accepts an end of 18:00` | yes |
| C42 | `ReservationApiTest.php` `it rejects a reservation outside business hours` | no - proves the 08:00–18:00 window on create, never the 4-hour maximum the criterion also fixes |
| C43 | walk - `Reservar` lands on the reservation page with that room selected | yes - `Reservar` on `/salas/1` and Sala 101 preselected on `/salas/1/reservar` |
| C44 | walk - the five reservation labels | yes - `Sala`, `Data`, `Hora Início`, `Hora Fim`, `Assunto` |
| C45 | `ReservationApiTest.php` `it stores one confirmed reservation` | yes - 201, the payload echoed, one row with `confirmed` |
| C46 | walk - success shows `Reserva criada com sucesso!` on the same page | no - the Walked line says success-message states were not clicked |
| C47 | `ReservationApiTest.php` `it rejects an empty subject` | yes - whitespace subject, 422, `Informe o assunto`, zero rows |
| C48 | `ReservationApiTest.php` `it rejects a subject longer than 200 characters` | yes - 201 characters inside padding, 422, zero rows |
| C49 | `ReservationApiTest.php` `it rejects a reservation for an inactive room` | yes - 422, the sentence, zero rows |
| C50 | `ReservationApiTest.php` `it rejects an overlapping reservation` | yes - 409, exactly `Conflito de horário`, still one row |
| C51 | `ReservationApiTest.php` `it rejects a reservation whose end is not after its start` | yes |
| C52 | `ReservationApiTest.php` `it rejects a reservation before today` | yes |
| C53 | `ReservationApiTest.php` `it rejects a reservation outside business hours` | yes |
| C54 | `ReservationApiTest.php` `it rejects a reservation over 4 hours` | yes - 13:01 trips the maximum, zero rows |
| C55 | `ReservationApiTest.php` `it uses the first failing check on create` | yes - the same three stacked-failure requests, zero rows |
| C56 | `RoomApiTest.php` `it returns 404 when the room does not exist` | no - that test calls `GET /api/rooms/404`; nothing exercises an unknown room on `POST /api/reservations` |
| C57 | `ReservationApiTest.php` `it rechecks the conflict at create time` | yes - query returns `disponivel`, a conflicting row lands, create answers 409 |
| C58 | walk - a server failure shows `Não foi possível criar a reserva` | no - not clicked, and the page prefers the server `message` on a 5xx (see Findings) |
| C59 | `ReservationApiTest.php` `it confirms exactly one of two concurrent creates` | yes - two processes on one sqlite file, statuses `[201, 409]`, one confirmed row |
| C60 | `ReservationApiTest.php` `it stores two reservations that only touch` | yes - both 201 and two confirmed rows |
| C61 | `ReservationApiTest.php` `it ignores an unconfirmed reservation` | yes - `disponivel` over the unconfirmed row and the create is 201 |
| C62 | walk - `Nova sala` shows the room form | yes - `Nova sala` on `/salas` and the form on `/salas/nova` |
| C63 | walk - the form fields with `Ativa` selected | yes - Nome, Capacidade, Recursos, `Ativa` checked |
| C64 | walk - `Editar` shows the form filled with the current room | no - the walk never opened `/salas/{id}/editar` |
| C65 | `RoomApiTest.php` `it creates a room` | yes - 201, the resource payload, and the stored row |
| C66 | `RoomApiTest.php` `it rejects an empty room name` | yes - whitespace name, 422, the sentence, zero rows |
| C67 | `RoomApiTest.php` `it rejects a room name over 100 characters` | yes - 101 characters, 422, zero rows |
| C68 | `RoomApiTest.php` `it rejects a capacity outside 1 to 999` | yes - 0, 1000, and `dez` all 422 with the sentence |
| C69 | `RoomApiTest.php` `it stores a room without resources` | yes - `resources` is `[]` and no tag row |
| C70 | `RoomApiTest.php` `it stores one tag for a repeated resource` | yes - three spellings store one row keeping `Projetor` |
| C71 | `RoomApiTest.php` `it allows two rooms to share a name` | yes - both 201, two rows |
| C72 | `RoomApiTest.php` `it updates a room` | yes - 200 and name, capacity, resources, and `active` persisted |
| C73 | walk - a failed save shows `Não foi possível salvar a sala` | no - the Walked line says error states were not clicked |
| C74 | `RoomApiTest.php` `it does not expose a room delete route` | yes - `DELETE` answers 404 or 405 and the room still exists; the status assertion is loose but either value proves no delete |
| C75 | `RoomApiTest.php` `it inactivates a room` | yes at HTTP - posts no body, 200, `active` false; the `Inativa` label is covered by C4's and C13's walk |
| C76 | `RoomApiTest.php` `it activates a room` | yes - 200 and `active` true |
| C77 | `RoomApiTest.php` `it keeps confirmed reservations when a room is inactivated` | yes - the confirmed row is still there after inactivating |
| C78 | `RoomApiTest.php` `it keeps the room row when a room is inactivated` | yes - `assertModelExists` after the call |
| C79 | `ReservationApiTest.php` `it rejects a reservation for an inactive room` | yes - 422, the sentence, zero rows |
| C80 | `ReservationApiTest.php` `it serves salas actions with no credentials` | yes - all eight actions, none 401 or 403, each with its spec status |

## Findings

| Finding | Evidence | Fix | Closed |
| --- | --- | --- | --- |
| Twelve walk rows are recorded `pass (Walked)` for states the walk says it never reached | tasks.md `## Handoff` `**Walked:**` ends with "Loading, error, empty-filter, and success-message states were not clicked in the browser", yet `proofs.md` marks C6, C7, C8, C9, C14, C16, C17, C21, C22, C46, C58, C73 as `pass (Walked)` | walk the loading, error, retry, empty-catalog, no-match, `Limpar`, success, create-failure, and save-failure states and record each in the Walked line, or move those criteria to a proof that can hold them | 90c9e03 |
| C64 is recorded `pass (Walked)` but the edit form was never opened | the Walked line lists `/salas`, `/salas/1`, `/salas/5`, `/salas/nova`, `/salas/1/reservar` and no `/salas/{id}/editar` | open `Editar` on a seeded room and record that the form carries that room's name, capacity, resources, and active flag | 90c9e03 |
| C10 is recorded `pass (Walked)` but no list taller than the viewport was scrolled | the walk seeded 5 rooms in a 3-column grid at 1280×900, so the cards fit on one screen | seed more cards than the viewport, scroll `/salas`, and record that each card keeps its name, capacity line, and tags | 90c9e03 |
| A server failure on reservation create shows the server sentence, not `Não foi possível criar a reserva` (SALA-58) | `resources/js/pages/ReservationPage.tsx:48-52` falls back only when `error.message === ''`; a throwaway test that made `CreateReservation::handle` throw returned HTTP 500 with `{"message":"storage is down"}` (`Server Error` in production), which the page would print | fall back to `Não foi possível criar a reserva` for statuses other than 422, 409, and 404, and prove it | 90c9e03 |
| C56's test does not touch reservation create, so an unknown room on create is unproven | the named test is `RoomApiTest.php` `it returns 404 when the room does not exist`, which calls `GET /api/rooms/404`; `ReservationApiTest.php` has no unknown-room case. A throwaway `POST /api/reservations` with `room_id = 404` does answer 404 `Sala não encontrada` with no row, so the behavior is right and only the proof is missing | add a `ReservationApiTest.php` case that posts an unknown `room_id`, asserts 404, `Sala não encontrada`, and zero rows, and point C56 at it | 90c9e03 |
| C42 proves the window on create but not the 4-hour maximum the criterion also fixes | `it rejects a reservation outside business hours` only submits 07:00–09:00; the maximum on create is asserted by C54's test instead | assert both halves of SALA-42 in C42's test, or name both tests on the check | 90c9e03 |
| C27's proof is C28's test, so the overlap predicate's strict bounds are not pinned by it | both checks name `it marks an overlapping active room indisponivel`, a single 09:00–10:00 against 09:30–10:30 case; a `<=`/`>=` predicate would still pass it | point C27 at a case that fails under non-strict bounds, or fold the touching case into it | 90c9e03 |
| Nothing renders `Disponível` or `Indisponível`, so the availability copy has no proof | the API returns `disponivel`/`indisponivel` and `resources/js/pages/SalasPage.tsx:202-209` maps them to the accented labels; the walk never ran the availability query and the capture predates it. Per the Test Coverage Matrix a visible state needs a walk or a browser proof | run the availability query in the walk and record both labels on the cards | 90c9e03 |
| `PUT /api/rooms/{room}` deletes every resource tag when the request omits `resources`, while an omitted `active` is preserved | `app/Http/Controllers/RoomController.php:87-93` passes `$data['resources'] ?? []` to `syncResources`, which deletes the room's tags first; `active` uses `?? $room->active` | keep the stored tags when `resources` is absent, or state in the spec that an update always replaces the full tag list | 90c9e03 |
| `proofs.md` was produced at 562521a, one commit before the verified HEAD, and its `**Result**` line counts a failure | `proofs.md` header reads `**HEAD**: 562521a` and `**Result**: 83 pass, 1 fail` while HEAD is 8f05c43; the only non-passing row is C1, marked `not judged` | rerun `check_proofs.py salas` at the closing HEAD so the gate reads proofs for the verified tree | 90c9e03 |

**Own examples** (throwaway, not committed, run at 8f05c43 and removed):

- A confirmed 09:00–13:00 reservation that *contains* the queried 10:00–11:00 window - no check names containment, only partial overlap and touching. The query returned `indisponivel` and the create returned 409. Holds.
- A room name of exactly 100 characters wrapped in spaces - the global `TrimStrings` middleware trims before `max:100`, so the create returned 201 and stored the trimmed name. Holds.
- `resource=' VIDEOCONFERÊNCIA '` against a stored `Videoconferência` tag - `Str::lower` is multibyte-aware on both sides, so the accented tag matched. Holds.
- An empty catalog and a 50-room catalog over `GET /api/rooms` - 0 and 50 rows, no error on the empty `whereIn` for availability. Holds.
- A 500 raised inside reservation create - no row written, but the JSON `message` reaches the page (see the SALA-58 finding).
