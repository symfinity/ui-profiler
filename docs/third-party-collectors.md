# Third-party collectors (051 greenfield)

Symfinity-owned profiler chrome covers Symfony stock collectors (CO-01–19). Third-party bundles register via standard `data_collector` tags.

## Policy

| Case | Visual | Functional |
|------|--------|------------|
| Stock Symfony collector | ux-blocks markup in `templates/chrome/` | Parity manifest row required |
| Product collector with `ui_profiler.menu` tag | Menu registry + optional panel override | Menu + panel smoke |
| Unmigrated third-party panel | Browser-default inside ux-blocks shell | Data visible; `sf-*` styling out of scope |

## Adding a collector

1. Register `data_collector` service (unchanged Symfony contract).
2. Optional: tag `ui_profiler.menu` with label, route, icon metadata.
3. Optional: add `templates/chrome/{VendorBundle}/Collector/{name}.html.twig` override using ux-blocks only.

See [greenfield-implement-rules.md](greenfield-implement-rules.md) for collector block compatibility and menu registry (`ui_profiler.menu` tag).
