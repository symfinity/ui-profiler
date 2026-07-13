# Installation

## Prerequisites

1. Add the [symfinity/recipes](https://github.com/symfinity/recipes) Flex endpoint to your project's `composer.json` (see [recipes README](https://github.com/symfinity/recipes/blob/main/README.md)).
2. Install the themed stack the profiler decorates:

```bash
composer require symfinity/ui-kernel symfinity/ux-blocks-core symfinity/ux-blocks-extended
```

Include UI Kernel head partials in your base layout — see [ui-kernel quickstart](https://github.com/symfinity/ui-kernel/blob/main/docs/quickstart.md).

## Composer

```bash
composer require symfinity/ui-profiler --dev
```

## Symfony Flex

The `0.1` recipe applies:

| Step | Result |
|------|--------|
| Bundle | `Symfinity\UiProfiler\UiProfilerBundle` registered for **`dev` and `test` only** |
| Config | `config/packages/symfinity_ui_profiler.yaml` copied from package defaults |

Requires `symfony/web-profiler-bundle` (pulled transitively in dev).

## Manual installation

When Flex is unavailable:

1. `composer require --dev symfinity/ui-profiler symfony/web-profiler-bundle`
2. Register `Symfinity\UiProfiler\UiProfilerBundle` for **`dev` and `test`** only in `config/bundles.php`
3. Copy `config/packages/symfinity_ui_profiler.yaml` from the package into your project

## Verify installation

```bash
php bin/console debug:config symfinity_ui_profiler
```

See [Verification](verification.md) for WDT smoke checks.

## Next steps

[Quick start](quickstart.md).
