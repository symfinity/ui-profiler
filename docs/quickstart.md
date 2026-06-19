# symfinity/ui-profiler — maintainer quickstart

Greenfield Chameleon profiler — **UI Kernel + ux-blocks**, zero owned CSS files.

**Read first:** [greenfield-implement-rules.md](greenfield-implement-rules.md)

## QA gates

```bash
cd src/symfinity

# Full package gate (default before merge)
./sbin/php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist

# Parity subset
./sbin/php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist --group ui-profiler-parity

# Zero CSS + asset ownership
./sbin/php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist \
  --filter 'GreenfieldMarkupTest|CssBudgetTest|AssetOwnershipTest'

# Optional Stimulus path (when ux-runtime / StimulusBundle present)
./sbin/php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist --group ui-profiler-runtime
```

## Install (non-org consumer)

After R2 packages publish on Packagist:

```bash
composer require symfinity/ui-profiler --dev
```

Flex recipe `symfinity/ui-profiler` 0.1 registers `UiProfilerBundle` for `dev`/`test` and copies `config/packages/symfinity_ui_profiler.yaml`.

Pre-R2 / org checkout: path-repo `@dev` with explicit requires on `symfinity/ui-kernel`, `symfinity/ux-blocks-core`, `symfinity/ux-blocks-extended`, `symfinity/ui-action`.

## Dogfood smoke

```bash
# From org L0 — combined lab (recommended)
make dogfood-serve SLUG=ui-lab

# Standalone profiler overlay
make dogfood-serve SLUG=symfinity-ui-profiler
```

| URL / action | Check |
|--------------|-------|
| `/` | WDT visible; no FOUC |
| `/kernel` | WDT + theme badge |
| WDT → profiler | Shell two-column; sidebar collectors |
| `?panel=ui_kernel` | UI Kernel theme/CSS panel |
| Settings dialog | Theme + width toggles |
| `/` key | Focus search in panel when open |

Use the WDT token from the page you profiled — `/_profiler/latest` may point at an asset request.

## Keyboard affordances

| Key | Action |
|-----|--------|
| `/` | Focus first table search field (when not in an input) |
| `Esc` | Close settings `<dialog>` |

## Troubleshooting

| Symptom | Fix |
|---------|-----|
| Toolbar layout jump | Inline WDT skeleton in `toolbar_js.html.twig`; no external WDT CSS |
| Single-column profiler shell | Hard refresh; shell embeds grid CSS |
| System theme wrong | `scheme=auto` uses client `prefers-color-scheme`; cookie `symfinity_ui_kernel_scheme` |
| Width toggle no effect | Clear `localStorage['symfony/profiler/width']`; expect `width-normal` vs `width-full` |
| Unstyled third-party panel | Expected for unmigrated `sf-*` collector markup |
| Dogfood broken assets | Delete `var/dogfood/{slug}/public/assets/` (AssetMapper dev rule) |
| CssBudgetTest fails | Remove owned `.css` — use ux-blocks + PHP-embedded shell chrome only |
