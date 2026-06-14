# Upstream sync (051 greenfield)

Track Symfony WebProfilerBundle pin used for parity tests and manual smoke.

| Pin | Source | Notes |
|-----|--------|-------|
| `^7.4\|\|^8.0` → **7.4.13** | `src/symfinity` product monorepo `composer.lock` | **069** baseline 2026-06-14 — full suite **82/82**; `@group ui-profiler-parity` **53/53**; ZOC **8/8** |

## Process

1. After `@group ui-profiler-parity` green, record `symfony/web-profiler-bundle` constraint + resolved version here.
2. When Symfony bumps profiler Twig, diff upstream WebProfilerBundle templates for behavior reference only — **MUST NOT** copy CSS/JS from removed archive.
3. Re-run full package gate: `./sbin/php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist`.
