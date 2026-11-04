# App Layout verification

**Verdict**: FAIL
**Proofs**: `proofs.md` at b9d811e
**Verifier**: independent sub-agent (author != verifier)

## Checks

| Check | Test | Holds |
| --- | --- | --- |
| C1 | `browser captures/room-booking-layout.running.png` | no - single `/` capture; claim is any SPA route |
| C2 | `walk - open / and read the topbar logo text` | yes |
| C3 | `browser captures/room-booking-layout.running.png` | yes |
| C4 | `walk - open /, /salas, /minhas-reservas; topbar above content on each` | yes |
| C5 | `browser captures/room-booking-layout.running.png` | yes |
| C6 | `walk - click Dashboard; URL / and active tab style` | yes |
| C7 | `walk - click Salas; URL /salas and active tab style` | yes |
| C8 | `walk - click Minhas Reservas; URL /minhas-reservas and active tab style` | yes |
| C9 | `walk - open /salas and /minhas-reservas directly; matching tab is active on each` | yes |
| C10 | `walk - resources/css/app.css defines listed token hex values` | yes |
| C11 | `browser captures/room-booking-layout.running.png` | yes |
| C12 | `browser captures/room-booking-layout.running.png` | yes |
| C13 | `walk - placeholder content appears below nav tabs inside the container` | yes |
| C14 | `walk - no Reservas de Hoje stats cards, rooms grid, or Minhas Reservas list widgets` | yes |
| C15 | `AppLayoutTest::test_spa_shell_serves_unknown_path` | no - asserts Blade `id="app"` only, not chrome or not-found placeholder |

## Findings

| Finding | Evidence | Fix | Closed |
| --- | --- | --- | --- |
| C15 claim (chrome + not-found on unknown path) stays green if React shell or NotFoundPage is wrong | `tests/Feature/AppLayoutTest.php` `test_spa_shell_serves_unknown_path` only `assertOk` + `assertSee('id="app"')`; matrix chrome layer has no walk/browser proof on this check | Add a walk (or browser) proof that unknown path keeps topbar/nav and shows the not-found placeholder; keep the feature test for the Blade mount | pending-fix-commit |
| C1 claim (any SPA route shows capture composition) is not held by one dashboard shot | Check proof is only `captures/room-booking-layout.running.png` from `/`; `/salas` or `/minhas-reservas` could drop the frame and C1 would still pass | Capture or walk each SPA route’s topbar + content frame, or narrow C1’s proof text to the captured state and rely on C4 only for multi-route topbar | pending-fix-commit |

## Own examples tried

| Example | Rule probed | Result |
| --- | --- | --- |
| `GET /foo/bar/baz` (nested unknown; not named in checks) | Unknown SPA path still serves Blade `#app` | Held — 200 + `id="app"` under PHPUnit throwaway |
| `GET /salas/` (trailing slash; not named in checks) | Deep-link shell for a tab-like path variant | Held — 200 + `id="app"` under PHPUnit throwaway |
| `GET /api/does-not-exist` (API side of SPA fallback bound) | SPA fallback must not swallow `/api/*` | Held — 404 JSON, no `id="app"` |
