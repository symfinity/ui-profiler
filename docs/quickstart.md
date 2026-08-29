# Quick start

Greenfield UI Kernel profiler — **UI Kernel + ux-blocks** shell chrome; role CSS from ux-blocks tiers.

## Install

```bash
composer require symfinity/ui-profiler --dev
```

Flex recipe `symfinity/ui-profiler` 0.1 registers `UiProfilerBundle` for `dev`/`test` and copies `config/packages/symfinity_ui_profiler.yaml`.

Requires `symfinity/ui-kernel`, `symfinity/ux-blocks-core`, and `symfinity/ux-blocks-extended` (see [installation](installation.md)).

## Smoke check

| URL / action | Check |
|--------------|-------|
| `/` | WDT visible; Chameleon-themed chrome (no legacy Symfony yellow toolbar) |
| `/kernel` | WDT + theme badge |
| WDT → profiler | Shell two-column; sidebar collectors use kernel tokens |
| `?panel=time` | Timeline readable; styles from SCSS author pipeline |
| `?panel=ui_kernel` | UI Kernel theme/CSS panel |
| Settings dialog | Theme + width toggles |
| `/` key | Focus search in panel when open |

Use the WDT token from the page you profiled — `/_profiler/latest` may point at an asset request.

## Keyboard affordances

| Key | Action |
|-----|--------|
| `/` | Focus first table search field (when not in an input) |
| `Esc` | Close settings `<dialog>` |

## Tests

From the package directory:

```bash
composer test
# or focused parity subset:
vendor/bin/phpunit -c phpunit.xml.dist --group ui-profiler-parity
```

## Troubleshooting

| Symptom | Fix |
|---------|-----|
| Toolbar layout jump | Inline WDT skeleton in `toolbar_js.html.twig`; no external WDT CSS |
| Single-column profiler shell | Hard refresh; shell embeds grid CSS |
| System theme wrong | `scheme=auto` uses client `prefers-color-scheme`; cookie `symfinity_ui_kernel_scheme` |
| Width toggle no effect | Clear `localStorage['symfony/profiler/width']`; expect `width-normal` vs `width-full` |
| Unstyled third-party panel | Expected for unmigrated `sf-*` collector markup — see [Third-party collectors](third-party-collectors.md) |
| Owned CSS uses legacy `.sf-*` selectors | Run package PHPUnit — owned shell CSS must not reintroduce Symfony toolbar class vocabulary |
| Broken AssetMapper assets in dev | Delete `public/assets/` if you ran `asset-map:compile` and live CSS stops updating |
| CssBudgetTest fails (forks) | Remove owned `.css` — use ux-blocks + PHP-embedded shell chrome only |

## See also

- [Configuration](configuration.md)
- [CHANGELOG](../CHANGELOG.md)
- [CONTRIBUTING](../CONTRIBUTING.md)
- [GitHub Issues](https://github.com/symfinity/ui-profiler/issues)
