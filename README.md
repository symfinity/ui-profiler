# symfinity/ui-profiler

UI Kernel Web Profiler — rebuilt on **UI Kernel + ux-blocks** (051 greenfield), not a port of legacy 043/044.

## Stack

| Layer | Source |
|-------|--------|
| L0 look | `ui_kernel_css()` + PHP-embedded shell/WDT chrome (ZOC — no owned `.css` files) |
| L1 markup | ux-blocks-core + ux-blocks-extended |
| L3 interaction | Native `shell.js` / `wdt/index.js`; optional Stimulus when `symfony/stimulus-bundle` is registered |

Planning: symfinity **069** stabilization — [ROADMAP](../../../../specs/symfinity/symfinity/ROADMAP.md).

## Status

**Stabilizing** (**069**) — greenfield shell + WDT + stock collectors on ux-blocks; dogfood **`ui-lab`**.

Implement rules: [docs/greenfield-implement-rules.md](docs/greenfield-implement-rules.md)

## Install (Flex)

```bash
composer require symfinity/ui-profiler --dev
```

Requires `symfinity/ui-kernel`, `symfinity/ux-blocks-core`, `symfinity/ux-blocks-extended`, and `symfony/web-profiler-bundle` (recipe wires dev/test bundle + config).

Org path-repo consumers: use `@dev` until R2 packages publish @ `^0.1`.

## QA

```bash
cd src/symfinity
./sbin/php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist
```

Maintainer docs: [docs/quickstart.md](docs/quickstart.md) · [docs/upstream-sync.md](docs/upstream-sync.md)

## Dogfood smoke

```bash
# Org L0 — combined kernel / themer / profiler lab
make dogfood-serve SLUG=ui-lab

# Standalone profiler overlay
make dogfood-new SLUG=symfinity-ui-profiler PACKAGE=symfinity/ui-profiler VERSION='7.4.*'
make dogfood-serve SLUG=symfinity-ui-profiler
```

## EXPLICIT — MUST NOT regress

| Rule | Detail |
|------|--------|
| **No archive copy** | Do **not** restore or copy from removed `packages/temp/ui-profiler/` |
| **No `sf-*` owned markup** | Owned Twig/JS uses `<twig:*>` + `data-ui-profiler-*` (ZOC) |
| **No owned CSS files** | Exit cap **0** lines in `assets/**/*.css` and `Resources/private/**/*.css` |
