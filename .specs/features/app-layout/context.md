# App Layout Context

**Gathered:** 2026-11-04
**Spec:** `.specs/features/app-layout/spec.md`
**Status:** Ready for design

---

## Feature Boundary

Ship the shared application chrome from the room-booking mockup: topbar, primary nav tabs, content container, and design tokens. Page domain UI (dashboard cards, rooms grid, reservation tables/modals) stays out of this feature; the body is a route outlet with minimal placeholders.

---

## Implementation Decisions

### Common layout scope

- Topbar + nav-tabs + content area (user 1.A)
- Matches the three chrome layers on every screen in the mockup

### Nav tab behavior

- Tabs navigate real React routes: `/`, `/salas`, `/minhas-reservas` (user 2.A)
- Destination pages are placeholders only in this feature

### Logo

- Keep exact mockup label: `📅 Reserva de Salas` (user 3)

### Design tokens

- Extract mockup CSS variables (`--primary`, `--bg-secondary`, shadows, text colors, etc.) in this feature (user 4.A)

### Body content

- Content area is a slot/outlet for child routes; no health-check UI on the main shell (user 5.A)
- Domain content from the mockup body is deferred

### Agent's Discretion

- Exact React Router setup and folder placement for layout components
- Placeholder copy on empty route pages (minimal, non-domain)
- Whether tokens live in CSS modules, a global CSS file, or Tailwind theme mapping — as long as values match the mockup

### Declined / Undiscussed Gray Areas → Assumptions

- Sticky header: not drawn as sticky in the mockup; stay in normal document flow
- Mobile header redesign: mockup only collapses grids at 768px; keep topbar/nav structure, stack nav if needed for readability

---

## Specific References

- Binding mockup: `.specs/ui/index.html` (copied to `captures/room-booking-layout.html`)
- User answers: `1. A`, `2. A`, `3. Manter`, `4. A`, `5. A`

---

## Deferred Ideas

- Dashboard "Reservas de Hoje", stats cards, Nova Reserva
- Salas grid and room cards
- Minhas Reservas table and cancel/edit flows
- Reservation modals and form validation
- Re-surfacing API health status in the UI
