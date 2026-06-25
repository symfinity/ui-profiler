# Changelog

All notable changes to `symfinity/ui-profiler` are documented here.

## Unreleased

### Added

- Flex recipe `symfinity/ui-profiler` 0.1 for dev/test bundle registration.
- Distribution split row in product monorepo `mono.json`.

### Changed

- **122** Sass refactor: modular SCSS (`shell/`, `wdt/`, `shared/` partials) with nesting, tokens, mixins; expanded compile output.
- **122** slices 1–5: core `request-status`/`input`/`filter-bar` roles + typography utilities; request summary → Badge/Stack; logger filter → core; collector utility purge; WDT ajax trim; **157** shell + **100** WDT + **32** critical CSS lines; **88/88** PHPUnit green.
- Stabilization pass (**069**): docs aligned to `ui-lab` dogfood; archive `packages/temp/ui-profiler/` removed.
- Semver branch-alias target `0.1.x-dev` (tag `v0.1.0` at release).

### Fixed

- Package QA baseline 82 PHPUnit tests green (parity 53, ZOC 8).

## 0.2.0-dev (051 greenfield)

- Greenfield UI Kernel profiler reboot on UI Kernel + ux-blocks (GC-6).
