# End-to-End Testing

The plugin ships a [Playwright](https://playwright.dev/) suite in `tests/playwright/`.
`playwright.config.ts` defines two projects, and the project you pick decides both *which*
specs run and *what they run against*:

| Project | Specs | Target |
| --- | --- | --- |
| `local` | everything except `specs/live/**` | a throwaway `wp-env` multisite (`http://localhost:8889`) |
| `live`  | only `specs/live/**/*.spec.ts`     | a real, already-running installation (`https://msls.co`) |

The two are deliberately disjoint: `local` sets `testIgnore: ['**/specs/live/**']`
(`playwright.config.ts:39`) and `live` sets `testMatch: ['**/specs/live/**/*.spec.ts']`
(`playwright.config.ts:47`).

## Local suite

The local suite seeds its own multisite topology, so `wp-env` has to be running first. The
commands are listed in [CLAUDE.md](../CLAUDE.md) under *E2E Tests* — in short,
`npm run playwright:local` for the admin and frontend specs, and the `:visual` /
`:update-snapshots` scripts for the visual specs, which are only pixel-stable inside the
Playwright Linux container.

## Live suite

The live suite is a read-only smoke test. It opens `/testpage` on a running installation,
clicks through the language switchers, and asserts that the active link picks up the
`current_language` class. There is no login, no seeding and no `wp-env` involved: the spec
imports straight from `@playwright/test` rather than from `tests/playwright/fixtures/msls-fixtures.ts`,
so it gets no `seed` fixture and no storage state.

### Running it

```bash
npx playwright install chromium   # once; wp-env is not needed for the live suite

npm run playwright:live                                             # against msls.co
MSLS_LIVE_URL=https://staging.example.com npm run playwright:live   # against another host
```

There is no `.env` support in this repository — nothing loads env files, so `MSLS_LIVE_URL`
has to be exported in your shell or prefixed to the command. (And note that `.gitignore`
currently has no `.env` entry, so a file you create there would *not* be ignored.)

### What the target installation has to provide

The assertions in `tests/playwright/specs/live/testpage.spec.ts` are specific. The target
needs:

* a publicly reachable page at `/testpage` — no login wall
* a network offering the languages `de_DE` and `en_GB`
* a `.widget_mslswidget` container holding the links *de_DE Deutsch* and *en_GB English*
* an `.msls-menu` holding the links *de_DE* and *en_GB* (exact text) — **msls.co does not
  currently provide this**, see *Current status* below
* at least three switcher renderings inside `.entry-content` with *de_DE Deutsch* /
  *en_GB English* — the spec iterates `nth(0)` through `nth(2)`
* additional links inside `.entry-content` reading exactly *Deutsch* / *English*, for the
  translation-hint test
* the `current_language` class on whichever link is currently active

`/testpage` is part of the test fixture, not ordinary content. Rebuilding that page on
msls.co will break the suite.

### Current status

As of 2026-08-22 the suite is **5 passed, 1 failed** against msls.co. The failing test is
`testing with .msls-menu de_DE en_GB`: `/testpage` does not render an element with the
`msls-menu` class, while the site's Custom CSS rule (`.msls-menu a { display: inline-block; }`)
is still there.

That is **not** fixture drift — it is the plugin. Between commit `3afd781` and the 3.0.0
release the backwards-compatibility aliases were loaded inside a `plugins_loaded` callback,
which made `class_exists( 'lloc\Msls\MslsOptions' )` return `false` for the MslsMenu add-on,
so MslsMenu registered neither its `wp_nav_menu_items` filter nor its settings section.

The cause is fixed on this branch (commit `7e80283`, PR #690): `includes/aliases.php`,
`includes/deprecated.php` and `includes/api.php` are required at file-load time again. The
spec keeps failing against msls.co only because the site still runs the released 2.10.1
code. **Re-run it once 3.0.0 is deployed to msls.co — it is the release verification for
the add-on connector fix, and it should then be 6 passed.** Keep the spec either way: it is
the standing regression test for that bug.

### Pitfalls

**Always use the npm script.** `npx playwright test --project=live` on its own still lets
`globalSetup` run, and `globalSetup` only ever targets the local wp-env installation. The
live tests themselves pass either way, but the setup runs first and has side effects that
have nothing to do with the run:

* it re-seeds your local test environment — `seedTranslationLinkedPosts()` calls
  `wp post delete --force` for every `post_type=post` entry on all three subsites before
  recreating the demo posts (`global-setup.ts:193-199`), so local posts are gone
* it re-primes the admin storage states and rewrites
  `tests/playwright/artifacts/seed.json`
* it adds roughly half a minute of `npx wp-env run tests-cli` round-trips
* with `wp-env` stopped it fails outright, since every step shells out to that container

`npm run playwright:live` sets `MSLS_LIVE_ONLY=1`, which makes the setup return before any
of that happens (`tests/playwright/setup/global-setup.ts:259`).

**Never run the suite bare.** A plain `npm run playwright` or `npx playwright test`
executes *both* projects — so it hits msls.co with the live specs on top of seeding your
local environment. Use `playwright:local` while working locally.

**Docker is not an option here.** `tests/playwright/scripts/run-in-docker.sh` exists for
the visual baselines. It forwards neither `MSLS_LIVE_URL` nor `MSLS_LIVE_ONLY`, and every
caller passes `--project=local`. Run the live suite directly on the host.

### Why the other specs cannot be pointed at production

Only `specs/live/` is portable. The rest is wired to localhost by construction:

* `specs/frontend/*` and `specs/visual/*` depend on the `seed` fixture
  (`tests/playwright/artifacts/seed.json`, which stores localhost links) and pin
  `test.use({ baseURL: subsiteUrl(slug) })` to `WP_BASE_URL`
* `specs/admin/*` and the visual admin spec need the storage states primed by
  `globalSetup`, which carry localhost cookies
* the committed baselines in
  `tests/playwright/specs/__snapshots__/visual/frontend.visual.spec.ts/hreflang-*.txt`
  contain literal `http://localhost:8889` URLs

### Not part of CI

`.github/workflows/e2e.yml` only ever runs `npm run playwright:local`. The live suite
depends on an external host and on that host's content, so a msls.co outage or an edit to
`/testpage` would turn unrelated pull requests red. Run it manually when you want to
verify a release against the real site.

### Practical notes

* **No trace on the first failure.** Outside CI `retries` is `0`
  (`playwright.config.ts:13`) while `trace` is `'on-first-retry'`
  (`playwright.config.ts:25`), so nothing is captured. Append `--trace=on` or
  `--retries=1` when you need to debug.
* **The run is parallel.** `fullyParallel: true` (`playwright.config.ts:11`) applies to
  `live` too, and `workers` is unbounded outside CI (`playwright.config.ts:14`), so several
  browsers hit the site at once. Add `--workers=1` to be gentle.
* **Reports** land in `tests/playwright/artifacts/` (entirely gitignored) and are shared
  with local runs: `html-report/`, `test-results/`, `test-results.json`.
* **The suite only reads.** It clicks frontend links; it never authenticates and never
  issues a writing request against production.
* **Known issue:** the `testing translation hint` test clicks a link named *English* right
  after asserting `toHaveCount(0)` for that same name. The test currently passes, but the
  intent is muddled — don't mistake a later fix for a regression.

## Environment variables

| Variable | Default | Effect | Read at |
| --- | --- | --- | --- |
| `MSLS_LIVE_URL` | `https://msls.co` | `baseURL` of the `live` project | `playwright.config.ts:6` |
| `MSLS_LIVE_ONLY` | unset | `1` skips all seeding and auth in `globalSetup` | `global-setup.ts:259` |
| `WP_BASE_URL` | `http://localhost:8889` | `baseURL` of the `local` project, and the host `globalSetup` seeds | `playwright.config.ts:5`, `global-setup.ts:7`, `msls-fixtures.ts:12` |
| `MSLS_SKIP_E2E_SEED` | unset | `1` skips seeding but keeps the local target — set by `run-in-docker.sh` | `global-setup.ts:255` |
| `STORAGE_STATE_DIR` | `tests/playwright/artifacts/storage-states` | where admin storage states are written and read | `global-setup.ts:10`, `msls-fixtures.ts:14` |
| `CI` | unset | enables `forbidOnly`, `retries: 2`, `workers: 1` | `playwright.config.ts:4` |
