# salas proofs

**HEAD**: 90c9e03
**Tree**: clean
**Shell**: /bin/sh
**Result**: 84 pass, 0 fail

| Check | Proof | Result |
| --- | --- | --- |
| C1 | `browser captures/salas-list.running.png` | pass |
| C2 | `walk - open /salas and read the heading Salas` | pass (Walked) |
| C3 | `walk - on /salas, an active room card shows Ativa` | pass (Walked) |
| C4 | `walk - on /salas, an inactive room card shows Inativa` | pass (Walked) |
| C5 | `run salas / it lists rooms over HTTP` | pass |
| C6 | `walk - with no rooms, /salas shows Nenhuma sala cadastrada` | pass (Walked) |
| C7 | `walk - slow the list request and see Carregando salas...` | pass (Walked) |
| C8 | `walk - fail GET /api/rooms and see that sentence and Tentar novamente` | pass (Walked) |
| C9 | `walk - use Tentar novamente and see a new GET /api/rooms` | pass (Walked) |
| C10 | `walk - with more cards than the viewport, scroll /salas and each card stays intact` | pass (Walked) |
| C11 | `run salas / it shows one room` | pass |
| C12 | `walk - open an active room and see Reservar and Editar` | pass (Walked) |
| C13 | `walk - open an inactive room, see Inativa, and see no Reservar` | pass (Walked) |
| C14 | `walk - slow the detail request and see Carregando sala...` | pass (Walked) |
| C15 | `run salas / it returns 404 when the room does not exist` | pass |
| C16 | `walk - fail the detail request and see that sentence and Tentar novamente` | pass (Walked) |
| C17 | `walk - use Tentar novamente on the detail error and see a new detail request` | pass (Walked) |
| C18 | `run salas / it filters rooms by minimum capacity` | pass |
| C19 | `run salas / it filters rooms by resource ignoring case` | pass |
| C20 | `run salas / it applies capacity and resource together` | pass |
| C21 | `walk - filter to a capacity and resource that match nothing and see Nenhuma sala encontrada` | pass (Walked) |
| C22 | `walk - use Limpar and see every room with empty filter fields` | pass (Walked) |
| C23 | `run salas / it returns every room when filters are blank` | pass |
| C24 | `walk - the Salas list has no name field and no location field` | pass (Walked) |
| C25 | `run salas / it rejects an invalid minimum capacity` | pass |
| C26 | `run salas / it marks a free active room disponivel` | pass |
| C27 | `run salas / it treats a touching reservation as free` | pass |
| C28 | `run salas / it marks an overlapping active room indisponivel` | pass |
| C29 | `run salas / it marks a free active room disponivel` | pass |
| C30 | `run salas / it leaves an inactive room without availability` | pass |
| C31 | `run salas / it treats a touching reservation as free` | pass |
| C32 | `run salas / it ignores a reservation on another room` | pass |
| C33 | `run salas / it ignores a reservation on another date` | pass |
| C34 | `run salas / it rejects an availability query outside business hours` | pass |
| C35 | `run salas / it rejects an availability query over 4 hours` | pass |
| C36 | `run salas / it rejects an availability query whose end is not after its start` | pass |
| C37 | `run salas / it rejects an availability query before today` | pass |
| C38 | `run salas / it reports only the first failing availability check` | pass |
| C39 | `run salas / it accepts a duration of exactly 4 hours` | pass |
| C40 | `run salas / it accepts a start of 08:00` | pass |
| C41 | `run salas / it accepts an end of 18:00` | pass |
| C42 | `run salas / it rejects a reservation outside business hours` | pass |
| C43 | `walk - use Reservar on an active room and land on its reservation page with that room selected` | pass (Walked) |
| C44 | `walk - the reservation page shows those five labels` | pass (Walked) |
| C45 | `run salas / it stores one confirmed reservation` | pass |
| C46 | `walk - after a successful reserve, that sentence is on the same reservation page` | pass (Walked) |
| C47 | `run salas / it rejects an empty subject` | pass |
| C48 | `run salas / it rejects a subject longer than 200 characters` | pass |
| C49 | `run salas / it rejects a reservation for an inactive room` | pass |
| C50 | `run salas / it rejects an overlapping reservation` | pass |
| C51 | `run salas / it rejects a reservation whose end is not after its start` | pass |
| C52 | `run salas / it rejects a reservation before today` | pass |
| C53 | `run salas / it rejects a reservation outside business hours` | pass |
| C54 | `run salas / it rejects a reservation over 4 hours` | pass |
| C55 | `run salas / it uses the first failing check on create` | pass |
| C56 | `run salas / it returns 404 when the reservation room does not exist` | pass |
| C57 | `run salas / it rechecks the conflict at create time` | pass |
| C58 | `walk - force a server failure on create and see Não foi possível criar a reserva with no new row` | pass (Walked) |
| C59 | `run salas / it confirms exactly one of two concurrent creates` | pass |
| C60 | `run salas / it stores two reservations that only touch` | pass |
| C61 | `run salas / it ignores an unconfirmed reservation` | pass |
| C62 | `walk - use Nova sala and see the room form` | pass (Walked) |
| C63 | `walk - the create form shows those fields and Ativa is selected` | pass (Walked) |
| C64 | `walk - use Editar and see the current name, capacity, resources, and active flag` | pass (Walked) |
| C65 | `run salas / it creates a room` | pass |
| C66 | `run salas / it rejects an empty room name` | pass |
| C67 | `run salas / it rejects a room name over 100 characters` | pass |
| C68 | `run salas / it rejects a capacity outside 1 to 999` | pass |
| C69 | `run salas / it stores a room without resources` | pass |
| C70 | `run salas / it stores one tag for a repeated resource` | pass |
| C71 | `run salas / it allows two rooms to share a name` | pass |
| C72 | `run salas / it updates a room` | pass |
| C73 | `walk - fail a save and see Não foi possível salvar a sala` | pass (Walked) |
| C74 | `run salas / it does not expose a room delete route` | pass |
| C75 | `run salas / it inactivates a room` | pass |
| C76 | `run salas / it activates a room` | pass |
| C77 | `run salas / it keeps confirmed reservations when a room is inactivated` | pass |
| C78 | `run salas / it keeps the room row when a room is inactivated` | pass |
| C79 | `run salas / it rejects a reservation for an inactive room` | pass |
| C80 | `run salas / it serves salas actions with no credentials` | pass |
| Build | `npm run build` | pass |
| Build | `npm run typecheck` | pass |
| Build | `vendor/bin/pint --dirty --format agent` | pass |
| Build | `php artisan test` | pass |

## Output

`Run salas` - exit 0

```
 ✔ it returns 404 when the room does not exist
 ✔ it updates a room
 ✔ it keeps resource tags when an update omits them
 ✔ it activates a room
 ✔ it inactivates a room
 ✔ it keeps confirmed reservations when a room is inactivated
 ✔ it keeps the room row when a room is inactivated

There was 1 PHPUnit test runner warning:

1) Options --no-output and --testdox cannot be used together

OK, but there were issues!
Tests: 77, Assertions: 270, PHPUnit Warnings: 1.
{"tool":"pest","result":"passed","tests":77,"passed":77,"assertions":270,"duration_ms":1244}
```
