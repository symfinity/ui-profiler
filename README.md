<div align="center">

# UI Profiler

### Web Profiler toolbar and panels rebuilt on UI Kernel and ux-blocks

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
- **ux-blocks markup** — core and extended components for profiler panels
- **Stock collectors** — Symfony Web Profiler data with refreshed layout
- **Zero owned CSS** — styling via ui-kernel and inline shell scripts
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

## Requirements

- PHP 8.2 or higher
- Symfony 7.4 or 8.x
- symfinity/ui-kernel, symfinity/ux-blocks-core, symfinity/ux-blocks-extended
- symfony/web-profiler-bundle

## Support

- [GitHub Issues](https://github.com/symfinity/ui-profiler/issues)
- [Security](.github/SECURITY.md)
- [Contributing](CONTRIBUTING.md)

## License

[MIT](LICENSE)
