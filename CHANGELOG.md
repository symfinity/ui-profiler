# Changelog

All notable changes to `symfinity/ui-profiler` are documented here.

## Unreleased

### Added

- Flex recipe `symfinity/ui-profiler` 0.1 for dev/test bundle registration.
- Distribution split row in product monorepo `mono.json`.

### Changed

- **122** shell delegation tiers 1–4: core `table`/`tabs`/`stat` role CSS in ux-blocks-core; profiler metrics/templates on `data-ui-role="stat"` + `[data-ui-metrics-grid]`; shell table/tabs/metrics blocks removed; `--header-*` token aliases collapsed to kernel tokens; tabpanels use `hidden` in `shell.js`; **279** shell CSS-body lines + **103** WDT (`active_wave` 8, exit gate ≤477); **88/88** PHPUnit green; ui-lab WDT smoke (T037) still open.
- Stabilization pass (**069**): docs aligned to `ui-lab` dogfood; archive `packages/temp/ui-profiler/` removed.
- Semver branch-alias target `0.1.x-dev` (tag `v0.1.0` at release).

### Fixed

- Package QA baseline 82 PHPUnit tests green (parity 53, ZOC 8).

## 0.2.0-dev (051 greenfield)

- Greenfield UI Kernel profiler reboot on UI Kernel + ux-blocks (GC-6).
