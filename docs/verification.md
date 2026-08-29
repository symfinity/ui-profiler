# Verification

**Integration profile:** P0 — dev/test Web Profiler chrome (Flex OOTB).

## Local commands

From the package directory:

```bash
composer test
composer phpstan
```

## Clean-app smoke

On a fresh Symfony **7.4+** project with `APP_ENV=dev`, themed stack, and Flex endpoint configured:

```bash
composer require symfinity/ui-kernel symfinity/ux-blocks-core symfinity/ux-blocks-extended
composer require --dev symfinity/ui-profiler
php bin/console debug:config symfinity_ui_profiler
```

Start the dev server and verify:

| URL / action | Check |
|--------------|-------|
| `/` | WDT visible; no flash of unstyled toolbar |
| `/kernel` or any themed route | WDT + theme badge when ui-kernel active |
| WDT → profiler | Two-column shell; sidebar collectors |
| `?panel=ui_kernel` | UI Kernel theme/CSS panel |
| Settings dialog | Theme + width toggles respond |

Use the WDT token from the page you profiled — `/_profiler/latest` may point at an asset request.

## Prod gate

With `APP_ENV=prod` and bundle **not** registered for prod, the WDT and profiler routes are absent (expected).

## See also

- [Quick start](quickstart.md)
- [Installation](installation.md)
- [CHANGELOG](../CHANGELOG.md)
