# app-layout screens

**Verifier**: independent sub-agent (author != verifier)
**Round**: 1
**pictures**: author's (server at :8010 not reachable from this agent’s network namespace; judged committed `captures/room-booking-layout.running.png` at 1280×800 from `/`)

| Screen | Compared with | Verdict | Finding |
| --- | --- | --- | --- |
| `captures/room-booking-layout.running.png` | `captures/room-booking-layout.html` | matches | 0 rows differ |

### `captures/room-booking-layout.running.png`

| Element | Result | Picture draws |
| --- | --- | --- |
| arrangement: full-width topbar above centered content frame | same | |
| page surface: `--bg-secondary` (`#F5F7FA`) behind chrome | same | |
| topbar surface: `--bg-primary` white with header shadow | same | |
| topbar logo copy: `📅 Reserva de Salas` | same | |
| topbar logo color: `--primary` (`#4A90E2`) | same | |
| topbar inner: logo left inside max-width 1200px padded frame | same | |
| content frame: centered max-width 1200px with horizontal padding under topbar | same | |
| nav tabs row inside content frame, bottom border `--border` | same | |
| nav tab labels: `Dashboard`, `Salas`, `Minhas Reservas` in that order | same | |
| active tab on `/`: `Dashboard` uses `.nav-tab.active` look (primary text + primary underline) | same | |
| inactive tabs: secondary text, no primary underline | same | |
| content outlet below nav (placeholder region present) | same | |
| Dashboard body widgets (`Reservas de Hoje`, Nova Reserva, stats cards) | same | approved departure AD-005 — minimal placeholder instead |
| domain cards / tables / reservation modals | same | approved departure AD-005 — absent |
