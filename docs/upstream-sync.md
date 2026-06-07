# Upstream sync (051 greenfield)

Track Symfony WebProfilerBundle pin used for parity tests and manual smoke.

| Pin | Source | Notes |
|-----|--------|-------|
| `^7.4\|\|^8.0` → **7.4.13** | `src/symfinity` product monorepo `composer.lock` | Parity green 2026-06-07 — full suite **76/76**; `@group ui-profiler-parity` **53/53**; `@group ui-profiler-runtime` **2/2** |

## Process

1. After `@group ui-profiler-parity` green, record `symfony/web-profiler-bundle` constraint + resolved version here.
2. When Symfony bumps profiler Twig, diff archive `packages/temp/ui-profiler/templates/chrome/` for behavior reference only — **MUST NOT** copy CSS/JS.
3. Re-run full package gate: `../../bin/php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist`.
