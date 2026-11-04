# app-layout proofs

**HEAD**: c31017f
**Tree**: clean
**Shell**: /bin/sh
**Result**: 19 pass, 0 fail

| Check | Proof | Result |
| --- | --- | --- |
| C1 | `walk - open /, /salas, and /minhas-reservas; each shows topbar logo and content frame (composition also in cap` | pass (Walked) |
| C2 | `walk - open / and read the topbar logo text` | pass (Walked) |
| C3 | `browser captures/room-booking-layout.running.png` | pass |
| C4 | `walk - open /, /salas, /minhas-reservas; topbar above content on each` | pass (Walked) |
| C5 | `browser captures/room-booking-layout.running.png` | pass |
| C6 | `walk - click Dashboard; URL / and active tab style` | pass (Walked) |
| C7 | `walk - click Salas; URL /salas and active tab style` | pass (Walked) |
| C8 | `walk - click Minhas Reservas; URL /minhas-reservas and active tab style` | pass (Walked) |
| C9 | `walk - open /salas and /minhas-reservas directly; matching tab is active on each` | pass (Walked) |
| C10 | `walk - resources/css/app.css defines listed token hex values` | pass (Walked) |
| C11 | `browser captures/room-booking-layout.running.png` | pass |
| C12 | `browser captures/room-booking-layout.running.png` | pass |
| C13 | `walk - placeholder content appears below nav tabs inside the container` | pass (Walked) |
| C14 | `walk - no Reservas de Hoje stats cards, rooms grid, or Minhas Reservas list widgets` | pass (Walked) |
| C15 | `walk - open /caminho-inexistente; topbar + nav remain and content shows Página não encontrada` | pass (Walked) |
| Build | `npm run build` | pass |
| Build | `npm run typecheck` | pass |
| Build | `vendor/bin/pint --dirty --format agent` | pass |
| Build | `php artisan test` | pass |
