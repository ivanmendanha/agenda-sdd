# LESSONS - auto-maintained by scripts/lessons.py

> Machine-owned. Do NOT hand-edit. Changes are overwritten on the next `lessons.py` write.
> Canonical state lives in `.specs/lessons.json`. Edit lessons only via the script.
> promote_threshold=2 distinct features · window_days=45 · quarantine_threshold=2

## Confirmed (load these at Plan/Checks)

Corroborated across multiple features. Safe to apply as guidance.

_none_

## Candidates (under observation - do NOT load as guidance yet)

Seen once or not yet corroborated. Tracked, not trusted.

### L-001 - For SPA chrome claims, use a walk or browser proof of the visible shell—not only a Blade mount assertion on id=app
- signal: `weak_test` · recurrence: 1 feature(s) · scope: `frontend` · harmful: 0
- features: app-layout
- evidence: C15 (frontend)
- last seen: 2026-11-04T22:16:40Z

### L-002 - When a criterion says every route shows chrome, prove each route (walk or capture)—a single page screenshot does not cover the others
- signal: `weak_test` · recurrence: 1 feature(s) · scope: `frontend` · harmful: 0
- features: app-layout
- evidence: C1 (frontend)
- last seen: 2026-11-04T22:16:40Z

### L-003 - Point a proof at a test that calls the same route and reads the same status the criterion fixes.
- signal: `weak_test` · recurrence: 1 feature(s) · scope: `api` · harmful: 0
- features: salas
- evidence: C56 (api)
- last seen: 2026-11-06T19:10:11Z

### L-004 - When one criterion fixes two limits, the named test asserts both limits.
- signal: `weak_test` · recurrence: 1 feature(s) · scope: `api` · harmful: 0
- features: salas
- evidence: C42 (api)
- last seen: 2026-11-06T19:10:11Z

### L-005 - Prove a strict interval bound with a touching case, not with an overlap a non-strict comparison would also reject.
- signal: `weak_test` · recurrence: 1 feature(s) · scope: `api` · harmful: 0
- features: salas
- evidence: C27 (api)
- last seen: 2026-11-06T19:10:11Z

### L-006 - On a failure other than validation, conflict, or not-found, show the page fallback sentence instead of the server message.
- signal: `ac_gap` · recurrence: 1 feature(s) · scope: `ui` · harmful: 0
- features: salas
- evidence: SALA-58 (ui)
- last seen: 2026-11-06T19:10:12Z

### L-007 - When an update keeps an omitted flag, keep an omitted collection too, and test the request that leaves the key out.
- signal: `ac_gap` · recurrence: 1 feature(s) · scope: `api` · harmful: 0
- features: salas
- evidence: RoomController.php:93 (api)
- last seen: 2026-11-06T19:10:12Z

## Quarantined (failed when applied - ignore)

A confirmed lesson that recurred alongside failure. Kept for the maintainer to review.

_none_
