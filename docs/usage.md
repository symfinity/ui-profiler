# Usage

Day-to-day patterns after [Installation](installation.md) and [Quick start](quickstart.md).

## Web Debug Toolbar

With `APP_ENV=dev`, browse any route. The WDT appears with UI Kernel-themed chrome — no extra layout wiring.

Open the profiler from the toolbar token on the page you profiled. `/_profiler/latest` may point at an asset request — use the token from the current page when debugging layout.

## UI Kernel panel

From the profiler shell, open the **UI Kernel** collector (`?panel=ui_kernel`) to inspect active theme id, CSS layers, and token summary.

## Settings dialog

Use the profiler settings affordance to toggle theme scheme and toolbar width. Width preference persists in `localStorage['symfony/profiler/width']`.

## Third-party collectors

Unmigrated Symfony collectors may still render legacy `sf-*` markup — expected until those bundles adopt ux-blocks shell patterns. **Owned** profiler shell, WDT, and stock collector chrome use UI Kernel tokens and ux-blocks components only (no legacy Symfony toolbar CSS). VarDumper output keeps a narrow scoped compat stylesheet for dump trees — distinct from legacy shell styling.

See [Third-party collectors](third-party-collectors.md).

## See also

- [Verification](verification.md)
- [Configuration](configuration.md)
