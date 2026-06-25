<div align="center">

# UI Profiler

### Web Profiler toolbar and panels rebuilt with UI Kernel and UX Blocks

[![PHP Version](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)](composer.json)
[![Symfony](https://img.shields.io/badge/Symfony-7.4+-343434?style=flat&logo=symfony&logoColor=white)](composer.json)
<br/>
[![CI](https://github.com/symfinity/ui-profiler/actions/workflows/ci.yml/badge.svg)](https://github.com/symfinity/ui-profiler/actions/workflows/ci.yml)
<br/>
[![Release](https://img.shields.io/packagist/v/symfinity/ui-profiler.svg?style=flat&logo=packagist&logoColor=white)](https://packagist.org/packages/symfinity/ui-profiler)
[![Downloads](https://img.shields.io/packagist/dt/symfinity/ui-profiler.svg?style=flat&logo=packagist&logoColor=white)](https://packagist.org/packages/symfinity/ui-profiler)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat)](LICENSE)

</div>

> [!NOTE]
> **Read-only mirror.**
> See [CONTRIBUTING.md](CONTRIBUTING.md) for how to propose changes.

## Features

- **UI Kernel chrome** — theme tokens for toolbar and WDT shell
- **ux-blocks markup** — core Twig components for profiler panels
- **Stock collectors** — Symfony Web Profiler data with refreshed layout
- **Owned Sass pipeline** — shell/WDT CSS authored in `assets/scss/`, compiled to `assets/styles/`
- **Dev-only** — Flex recipe registers bundle for dev and test

## Prerequisites

Add the [symfinity/recipes](https://github.com/symfinity/recipes) Flex endpoint to your project's `composer.json` (see [recipes README](https://github.com/symfinity/recipes/blob/main/README.md)) — recipes are not in Symfony's official recipe repository yet.

## Installation

Install with `--dev`. Requires themed ux-blocks stack.

```bash
composer require symfinity/ui-profiler --dev
```

## Quick Start

```bash
composer require symfinity/ui-profiler --dev
```

See [Quick start](docs/quickstart.md) for the full walkthrough.

## Documentation

- **[Quick start](docs/quickstart.md)** — minimal setup path
- **[Third-party collectors](docs/third-party-collectors.md)** — custom profiler panels
- **[Upstream sync](docs/upstream-sync.md)** — Symfony Web Profiler alignment

## Maintainer CSS (Sass pipeline)

Author shell and WDT rules in SCSS; commit compiled CSS (same pattern as `symfinity/ux-blocks-core`):

| Author | Ship |
|--------|------|
| `assets/scss/shell-chrome.scss` | `assets/styles/shell-chrome.css` |
| `assets/scss/wdt-toolbar.scss` | `assets/styles/wdt-toolbar.css` |
| `assets/scss/wdt-critical.scss` | `assets/styles/wdt-critical.css` |

```bash
cd packages/ui-profiler
bin/profiler-css-compile          # compile (requires Node/npx + dart-sass)
bin/profiler-css-compile --check  # CI freshness gate
composer scss:check               # same as --check
```

Runtime: `ProfilerCssProvider` reads committed CSS for inline `<style>` tags (WDT + shell). ux-blocks-core role CSS remains separate.

Author layout:

```text
assets/scss/
  shared/          — tokens + mixins (compile-time)
  shell/           — shell partials (@use from shell-chrome.scss)
  wdt/             — toolbar + critical partials
  shell-chrome.scss
  wdt-toolbar.scss
  wdt-critical.scss
```

## JavaScript layout

| Path | Role |
|------|------|
| `assets/controllers/` | Stimulus controllers (dialog, tabs, table-search) — AssetMapper + `UiProfilerExtension` |
| `assets/js/shell.js` | Profiler shell ES module (`UiProfilerShell`) |
| `assets/js/wdt.js` | Web Debug Toolbar ES module (`UiProfilerWdt`) |
| `assets/js/panels/*.js` | Collector panel boot scripts (time, form, events) |

Shell, WDT, and panel scripts are served by `ProfilerAssetController` at `/_profiler/assets/{path}` (not Stimulus — do not merge into `controllers/`).

## Requirements

- PHP 8.2 or higher
- Symfony 7.4 or 8.x
- symfinity/ui-kernel, symfinity/ux-blocks-core
- symfony/web-profiler-bundle

## Support

- [GitHub Issues](https://github.com/symfinity/ui-profiler/issues)
- [Security](.github/SECURITY.md)
- [Contributing](CONTRIBUTING.md)

## License

[MIT](LICENSE)
