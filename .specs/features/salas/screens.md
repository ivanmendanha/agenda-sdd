# Salas screens

**Verifier**: independent sub-agent (author != verifier)
**Round**: 1
**pictures**: mine — Chrome at `/usr/bin/google-chrome`, window 1280×900 (the capture's own pixel size, 1x), `http://127.0.0.1:8010/salas`, the state the `**Walked:**` line reaches with Sala 101/102/201/202 active and Sala 301 inactive. Saved over the author's `captures/salas-list.running.png`, not resized.

| Screen | Compared with | Verdict | Finding |
| --- | --- | --- | --- |
| `captures/salas-list.running.png` | `captures/salas-list.html`, SALA-01 | matches | 0 rows differ |

### `captures/salas-list.running.png`

| Element | Result | Picture draws |
| --- | --- | --- |
| arrangement: page surface `--bg-secondary` (`#F5F7FA`) behind a centred max-width 1200px container with 24px padding | same | page surface sampled `(245, 247, 250)`; content spans x 60–1219 inside the 1280 window, so the frame is centred at the capture's width |
| panel: one white `.card` (radius 8, `--shadow`) wrapping the title and the grid | same | the heading and the grid sit directly on the page surface, and the white panel between them holds the filters instead. The file's own comment fixes the binding at "the room card only", and the spec's source line binds "room card grid … name, `Capacidade: N pessoas`, feature tags, 3 columns" |
| panel title copy `Salas Disponíveis` (20px, 600) above the grid | same | approved departure Differences table (AD-008) — heading reads `Salas`, left-aligned above the grid, at a heavier/larger weight than the mockup title |
| grid: `.grid-3`, 3 equal columns, 20px gap, at a viewport wider than 768px | same | 3 columns at 1280 wide: white runs x 60–432, 453–826, 847–1219 (373/374/373px) with 21px gutters. Row 1 holds `Sala 101`, `Sala 102`, `Sala 201`; row 2 holds `Sala 202`, `Sala 301` and leaves the third cell empty |
| room card surface: white, radius 8, `--shadow`, 20px padding | same | card fill sampled `(255, 255, 255)`; cards in a row share one height (row 1 y 449–606, row 2 y 629–818) |
| room card: room name first, 18px / 600 | same | `Sala 101`, `Sala 102`, `Sala 201`, `Sala 202`, `Sala 301`, each the top line of its card |
| room card: capacity line `Capacidade: N pessoas` below the name, `--text-secondary`, 14px | same | `Capacidade: 8 pessoas`, `Capacidade: 4 pessoas`, `Capacidade: 12 pessoas`, `Capacidade: 6 pessoas`, `Capacidade: 20 pessoas` — one per card, each matching that card's room |
| room card: feature tags row below the capacity line, flex, 8px gap, wrapping | same | `Projetor` `Quadro Branco` `Videoconferência`; `Quadro Branco` `TV`; `Projetor` `Sistema de Som` `Videoconferência`; `Projetor` `Quadro Branco`; and on `Sala 301` four tags wrapping to a second line — `Palco` `Projetor` `Sistema de Som` / `Videoconferência`. Tag order is alphabetical rather than the mockup's order; the file calls its names "the mockup's sample data, not a seed requirement" |
| feature tag: chip on `--bg-secondary`, radius 4, 12px, `--text-secondary` | same | grey chips with light fill, smaller than the capacity line |
| room card: no situation label | same | approved departure Differences table (AD-008) — each card ends with a situation line: `Ativa` in green on Sala 101, 102, 201 and 202, `Inativa` in grey on Sala 301. The `Inativa` grey is lighter than the `Ativa` green and the lowest-contrast text in the picture |
| room card: no location field | same | no location text on any card — name, capacity, tags, situation only |
| room card: no image or thumbnail | same | no image region on any card |
| no capacity or resource filter controls | same | approved departure Differences table (AD-009) — a filter panel above the grid draws `Capacidade mínima` and `Recurso` text inputs with `Filtrar` (filled) and `Limpar` (outline) |
| no availability query on the list | same | approved departure Differences table (AD-009) — a second filter row draws `Data` (`dd/mm/aaaa`), `Hora Início` (`--:--`), `Hora Fim` (`--:--`) and `Consultar disponibilidade`. No `Disponível` / `Indisponível` mark is drawn, which is this state: no interval has been submitted |
| no room create, edit, activate or inactivate control | same | approved departure Differences table (AD-006) — `Nova sala` sits at the right of the heading row. `Editar`, `Ativar` and `Inativar` live on the room detail, another state |
| no app topbar or nav tabs in the frame | same | the verified app-layout chrome is present above the heading: `📅 Reserva de Salas` topbar and the tabs `Dashboard`, `Salas`, `Minhas Reservas` with `Salas` active (primary text, primary underline). Bound by `app-layout/captures/room-booking-layout.html`, not by this file |
| render: nothing overlapping, cut off or scrolled out of the window | same | the last card ends at y 818 inside the 900px window, so all five cards, both filter rows and the heading are fully drawn |
| single column when the viewport is 768px or narrower | same | another state — this picture is 1280 wide |
