# symfinity/ui-profiler — maintainer quickstart (051)

Greenfield Chameleon profiler — **UI Kernel + ux-blocks**, zero owned CSS files.

**Read first:** [greenfield-implement-rules.md](greenfield-implement-rules.md)

## QA gates

```bash
cd src/symfinity

# Full package gate (default before merge)
./bin/php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist

# Parity subset
./bin/php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist --group ui-profiler-parity

# Zero CSS + asset ownership
./bin/php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist \
  --filter 'GreenfieldMarkupTest|CssBudgetTest|AssetOwnershipTest'

# Optional Stimulus path (when ux-runtime / StimulusBundle present)
./bin/php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist --group ui-profiler-runtime
```

## Dogfood smoke (GC-6)

```bash
# From org L0
make dogfood-serve SLUG=chameleon-showcase
```

| URL | Check |
|-----|-------|
| `/` | WDT stable; no FOUC; collector badges |
| `/_profiler/{token}?panel=request` | Greenfield shell (`data-ui-profiler-shell`); sidebar menu; collector tabs |
| `/_profiler/{token}?panel=time` | Timeline panel |
| Profiler settings | Theme System/Light/Dark; Fixed width / Fit to window |

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
| Single-column profiler shell | Hard refresh; shell embeds grid CSS (schema 1.0 dogfood) |
| System theme wrong | `scheme=auto` uses client `prefers-color-scheme`; cookie `symfinity_ui_kernel_scheme` |
| Width toggle no effect | Clear `localStorage['symfony/profiler/width']`; expect `width-normal` (max 75rem) vs `width-full` |
| Unstyled third-party panel | Expected for unmigrated `sf-*` collector markup |
| Dogfood broken assets | Delete `var/dogfood/{slug}/public/assets/` (AssetMapper dev rule) |
| CssBudgetTest fails | Remove owned `.css` — use ux-blocks + PHP-embedded shell chrome only |
