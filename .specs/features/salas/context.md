# Salas Context

**Gathered:** 2026-11-06
**Spec:** `.specs/features/salas/spec.md`
**Status:** Ready for design

---

## Feature Boundary

HU-002, the full PDF: list rooms, show details, filter, query availability, create a reservation on its own page, and let any visitor create, edit, activate, and deactivate rooms. No login.

---

## Implementation Decisions

### Scope

- User chose the full PDF, including room administration, without login.
- Question: "O que esta spec deve cobrir?" Option: "O PDF inteiro, inclusive cadastrar/editar/ativar/inativar salas, sem login: qualquer visitante faz o CRUD (afasta CA08 e AD-001)."
- Recorded as AD-006. AD-001 still means there is no authentication.

### Room attributes

- User chose the mockup set.
- Option: "Nome e capacidade obrigatórios; recursos opcionais; ativa (sim/não, padrão ativa). Sem localização e sem imagem."

### Inactive rooms

- User chose to show them in the consultation.
- Option: "A consulta também mostra salas inativas, marcadas como inativas, e bloqueia nova reserva nelas."
- Recorded as AD-008.

### Filters and availability

- User chose minimum capacity, resource, and a free interval.
- Option: "Filtros de capacidade mínima e recurso. Disponibilidade por intervalo livre (data + início + fim), 08:00–18:00, duração máxima de 4 horas. Sem localização e sem horários pré-definidos."
- Recorded as AD-009.

### Reservation screen

- User chose a dedicated page and no maintenance block.
- Option: "Página própria de reserva, não o modal. Sem bloqueio de manutenção."
- Recorded as AD-007.

### Plan defaults confirmed with the spec plan

- No limit of 3 active reservations (AD-010).
- No search by name.
- List heading is `Salas`.
- "Today" follows the application timezone (`UTC` in `config/app.php`).
- Intervals that only touch do not conflict.
- Name length 1–100. Capacity integer 1–999. Duplicate resources on one room are stored once.
- After a successful reservation, the page shows `Reserva criada com sucesso!` and stays there.

### Where the rules run

- User chose: "Laravel decide tudo; o React só exibe. Filtro, disponibilidade e conflito ficam na API. A criação concorrente trava a sala numa transação, o que funciona no SQLite. Testes Pest cobrem as regras HTTP."
- Recorded as AD-011. `lockForUpdate` is a no-op on sqlite in this Laravel version. The sqlite connection uses `IMMEDIATE` transactions and `busy_timeout` 5000 so the second writer waits and then sees the first row.

### Agent's Discretion

- URL paths, as long as the visitor can reach the list, the detail, the room form, and the reservation page.
- How resource names are entered, as long as the room stores a list and the card shows one tag per resource.
- Persistence and locking strategy, as long as the conflict rules and the concurrent-create outcome hold.

### Declined / Undiscussed Gray Areas → Assumptions

None. The open refinement questions in the PDF were answered. Plan defaults are written in the spec assumptions.

---

## Specific References

- `.specs/hu/HU-002-Salas.pdf`
- Room card composition from `.specs/ui/index.html`: name, `Capacidade: N pessoas`, feature tags, 3-column grid, one column at 768px and below.
- Reservation copy taken from the mockup script: business-hours, duration, end-before-start, conflict, and success messages.

---

## Deferred Ideas

- Recurrence, external calendars, notifications, maintenance blocks, and image upload (HU-002 out of scope).
- Dashboard and Minhas Reservas (later features).
- Cancelling a reservation.
- The mockup limit of 3 active reservations (AD-010).
