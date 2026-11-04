# React API Structure Context

**Gathered:** 2026-11-04
**Spec:** `.specs/features/react-api-structure/spec.md`
**Status:** Ready for design

---

## Feature Boundary

Scaffold the Laravel project so a React (Vite) SPA and a public Laravel JSON API can be developed in the same origin. No domain features (reservas/salas), no authentication, and no port of the mockup UI in this delivery.

---

## Implementation Decisions

### Frontend placement

- React lives inside Laravel at `resources/js` (option A)
- Same Vite pipeline already in the project; Laravel serves the SPA shell and the API on one origin

### Delivery depth

- Structure only (option A): React mount, folder layout, HTTP client helper, `routes/api.php`, example public endpoint
- Mockup UI from `.specs/ui/index.html` is deferred to a later feature

### Authentication

- None for the whole project (option B + academic constraint)
- User: "Este é um projeto unicamente com intuito de apresentação acadêmica, então o projeto não terá autenticação"
- No Sanctum, no login, no auth middleware on API routes

### Agent's Discretion

- Shape of the example public endpoint (health/ping)
- Exact React folder names under `resources/js` (components, pages, services, etc.)
- Whether `/` alone or a SPA fallback route serves the Blade shell

### Declined / Undiscussed Gray Areas → Assumptions

- Separate `frontend/` package: declined (chose integrated)
- Porting mockup now: declined
- Auth later: declined for the project (never)

---

## Specific References

- Mockup: `.specs/ui/index.html` (Sistema de Reserva de Salas) — reference for the eventual product UI; not built in this feature

---

## Deferred Ideas

- Port Dashboard / Salas / Minhas Reservas and modals from the mockup to React
- Domain API for rooms and reservations
- Any real auth (explicitly out of project intent)
