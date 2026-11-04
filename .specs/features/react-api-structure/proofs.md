# react-api-structure proofs

**HEAD**: 730e068
**Tree**: clean
**Shell**: /bin/sh
**Result**: 14 pass, 0 fail

| Check | Proof | Result |
| --- | --- | --- |
| C1 | `run feature / Root serves react shell` | pass |
| C2 | `npm run build` | pass |
| C3 | `walk - open / and see data-react-root marker from React` | pass (Walked) |
| C4 | `run feature / Health endpoint returns ok` | pass |
| C5 | `run feature / Health endpoint returns ok` | pass |
| C6 | `run feature / Health allows unauthenticated access` | pass |
| C7 | `walk - folders present under resources/js` | pass (Walked) |
| C8 | `walk - resources/js/services/api.js exports health helper used by App` | pass (Walked) |
| C9 | `walk - open / and see health status text from API` | pass (Walked) |
| C10 | `walk - with API stopped or forced failure, error text visible and root mounted` | pass (Walked) |
| C11 | `run feature / Unknown api path returns json` | pass |
| Build | `npm run build` | pass |
| Build | `vendor/bin/pint --test` | pass |
| Build | `php artisan test` | pass |

## Output

`Run feature` - exit 0

```
PHPUnit 12.5.43 by Sebastian Bergmann and contributors.
Runtime: PHP 8.5.4
Configuration: /home/ivanmendanha/projects/aulas/agenda-sdd/phpunit.xml
Time: 00:00.273, Memory: 16.00 MB
React Api Structure (Tests\Feature\ReactApiStructure)
 Health endpoint returns ok
 Health allows unauthenticated access
 Unknown api path returns json
 Root serves react shell
OK (4 tests, 11 assertions)
{"tool":"phpunit","result":"passed","tests":4,"passed":4,"assertions":11,"duration_ms":273}
```

`npm run build` - exit 0

```
public/build/assets/instrument-sans-400-normal-DRC__1Mx.woff2   16.86 kB
public/build/assets/instrument-sans-500-normal-Dk9ku72i.woff2   17.23 kB
public/build/assets/instrument-sans-600-normal-B7fBEWYG.woff2   17.40 kB
public/build/assets/instrument-sans-400-normal-D1W7dsQl.woff    21.24 kB
public/build/assets/instrument-sans-500-normal-Z6ESRlEs.woff    21.65 kB
public/build/assets/instrument-sans-600-normal-B9e8oLYv.woff    21.67 kB
public/build/assets/fonts-C9MNnjVw.css                           2.35 kB │ gzip:  0.38 kB
public/build/assets/app-C9dghHMD.css                            58.79 kB │ gzip: 12.33 kB
public/build/assets/app-7lKHSWCH.js                            219.45 kB │ gzip: 68.61 kB

✓ built in 618ms
npm warn Unknown env config "devdir". This will error in a future major version of npm. See `npm help npmrc` for supported config options.
npm notice run build
npm notice run vite build
[plugin laravel:fonts] Optimized font fallbacks require the optional "fontaine" package. Install it, or set "optimizedFallbacks: false" on your fonts to disable the feature.
```
