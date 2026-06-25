# Greenfield implement rules (051) — EXPLICIT

**Normative:** this file (package canon after **051** `DONE` spec extraction)

## MUST NOT (agents + maintainers)

1. **`cp -R` (or equivalent) from `packages/temp/ui-profiler/`** into `packages/ui-profiler/` for templates, assets, or CSS.
2. **Ship Symfony `sf-*` vocabulary** in owned Twig (`sf-toolbar-block`, `sf-tabs`, `table-with-search-field`, …).
3. **Mark tasks `[X]`** when markup is archive-derived — tests green on copied HTML is not 051 done.
4. **Port archive JS selectors unchanged** — rewrite for `data-ui-profiler-*` + ux-blocks DOM.

## MUST

1. **Look:** `ui_kernel_css()` (L0) + `ux_blocks_core_stylesheet()` (L1 role CSS inline).
2. **Markup:** ux-blocks-core Twig components (L1).
3. **Hooks:** `data-ui-profiler-wdt`, `data-ui-profiler-shell`, `data-ui-profiler-collector="{id}"`, etc.
4. **Archive use:** open a collector in temp **only** to list data fields and behaviors — then close and write fresh Twig.
5. **Gate:** `GreenfieldMarkupTest` + `CssBudgetTest` + `ShellLineBudgetTest` + `@group ui-profiler-parity` (PI-3 assertions — no stock class names).

## Stack (composer-stack)

`symfinity/ui-kernel` · `symfinity/ux-blocks-core` · suggest `symfinity/ux-runtime`

## Verification

```bash
cd src/symfinity
sbin/php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist \
  --filter 'GreenfieldMarkupTest|CssBudgetTest|AssetOwnershipTest'
```

Parity suite **SHOULD fail** until each phase is rebuilt on ux-blocks — that is expected during dark launch.
