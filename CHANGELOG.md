# Changelog

All notable changes to `symfinity/ui-profiler` are documented here.

## Unreleased

### Added

- Flex recipe `symfinity/ui-profiler` 0.1 for dev/test bundle registration.
- Distribution split row in product monorepo `mono.json`.

### Changed

- **122** shell delegation: **448** CSS-body lines (`active_wave` 8, exit gate ≤477 met); legacy layout IDs migrated to `data-ui-profiler-*` hooks (T034/T036); `ShellSelectorScopeTest` guards forbidden `#summary`/`#sidebar`/… selectors; tab/metric rules scoped under hooks; **88/88** PHPUnit green; ui-lab WDT smoke (T037) still open.
- Stabilization pass (**069**): docs aligned to `ui-lab` dogfood; archive `packages/temp/ui-profiler/` removed.
- Semver branch-alias target `0.1.x-dev` (tag `v0.1.0` at release).

### Fixed

- Package QA baseline 82 PHPUnit tests green (parity 53, ZOC 8).

## 0.2.0-dev (051 greenfield)

- Greenfield UI Kernel profiler reboot on UI Kernel + ux-blocks (GC-6).
