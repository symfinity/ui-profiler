# symfinity/ui-profiler (051 greenfield)

Chameleon Web Profiler — rebuilt on **UI Kernel + ux-blocks**, not a port of 043/044.

## Stack

| Layer | Source |
|-------|--------|
| L0 look | `ui_kernel_css()` + PHP-embedded shell/WDT chrome (ZOC — no owned `.css` files) |
| L1 markup | ux-blocks-core + ux-blocks-extended |
| L3 interaction | Native `shell.js` / `wdt/index.js`; optional Stimulus when `symfony/stimulus-bundle` is registered |

Spec: symfinity **051** `DONE` — planning row in [ROADMAP](../../../../specs/symfinity/symfinity/ROADMAP.md) only (disposable spec removed).

## Status

**DONE** (GC-6) — greenfield shell + WDT + P0 collectors on ux-blocks; dogfood `chameleon-showcase`.

Archive inspiration only: `packages/temp/ui-profiler/` (**MUST NOT** copy Twig/JS/CSS — see [greenfield-implement-rules.md](docs/greenfield-implement-rules.md)).

## QA

```bash
cd src/symfinity
./bin/php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist
```

Maintainer docs: [docs/quickstart.md](docs/quickstart.md) · [docs/upstream-sync.md](docs/upstream-sync.md)

## EXPLICIT — MUST NOT regress

| Rule | Detail |
|------|--------|
| **No archive copy** | Do **not** `cp` templates/JS/CSS from `packages/temp/ui-profiler/` |
| **No `sf-*` owned markup** | Owned Twig/JS uses `<twig:*>` + `data-ui-profiler-*` (ZOC — see [greenfield-implement-rules.md](docs/greenfield-implement-rules.md)) |
| **No owned CSS files** | Exit cap **0** lines in `assets/**/*.css` and `Resources/private/**/*.css` |

Implement rules: [docs/greenfield-implement-rules.md](docs/greenfield-implement-rules.md)
