# app-layout proofs

**HEAD**: b9d811e
**Tree**: clean
**Shell**: /bin/sh
**Result**: 14 pass, 5 fail

| Check | Proof | Result |
| --- | --- | --- |
| C1 | `browser captures/room-booking-layout.running.png` | **not judged** (screens.md) |
| C2 | `walk - open / and read the topbar logo text` | pass (Walked) |
| C3 | `browser captures/room-booking-layout.running.png` | **not judged** (screens.md) |
| C4 | `walk - open /, /salas, /minhas-reservas; topbar above content on each` | pass (Walked) |
| C5 | `browser captures/room-booking-layout.running.png` | **not judged** (screens.md) |
| C6 | `walk - click Dashboard; URL / and active tab style` | pass (Walked) |
| C7 | `walk - click Salas; URL /salas and active tab style` | pass (Walked) |
| C8 | `walk - click Minhas Reservas; URL /minhas-reservas and active tab style` | pass (Walked) |
| C9 | `walk - open /salas and /minhas-reservas directly; matching tab is active on each` | pass (Walked) |
| C10 | `walk - resources/css/app.css defines listed token hex values` | pass (Walked) |
| C11 | `browser captures/room-booking-layout.running.png` | **not judged** (screens.md) |
| C12 | `browser captures/room-booking-layout.running.png` | **not judged** (screens.md) |
| C13 | `walk - placeholder content appears below nav tabs inside the container` | pass (Walked) |
| C14 | `walk - no Reservas de Hoje stats cards, rooms grid, or Minhas Reservas list widgets` | pass (Walked) |
| C15 | `run feature / Spa shell serves unknown path` | pass |
| Build | `npm run build` | pass |
| Build | `npm run typecheck` | pass |
| Build | `vendor/bin/pint --dirty --format agent` | pass |
| Build | `php artisan test` | pass |

## Output

`Run feature` - exit 0

```
PHPUnit 12.5.43 by Sebastian Bergmann and contributors.
Runtime: PHP 8.5.4
Configuration: /home/ivanmendanha/projects/aulas/agenda-sdd/phpunit.xml
Time: 00:00.209, Memory: 16.00 MB
App Layout (Tests\Feature\AppLayout)
 Spa shell serves dashboard route
 Spa shell serves salas route
 Spa shell serves minhas reservas route
 Spa shell serves unknown path
 Api health still returns json with spa fallback
OK (5 tests, 10 assertions)
{"tool":"phpunit","result":"passed","tests":5,"passed":5,"assertions":10,"duration_ms":210}
```
