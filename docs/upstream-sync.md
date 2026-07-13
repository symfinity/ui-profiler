# Upstream sync

UI Profiler decorates Symfony's Web ProfilerBundle — it does not fork profiler data collection.

## What we track

| Upstream | Role |
|----------|------|
| `symfony/web-profiler-bundle` | Collector data, routes (`/_wdt`, `/_profiler`), toolbar token semantics |
| `symfony/twig-bundle` | Twig panel and template override hooks |
| Stock bundle collector templates | Reskinned under `templates/chrome/{BundleName}/` |

## What we own

| Area | Location |
|------|----------|
| Toolbar + profiler shell chrome | `assets/scss/shell/`, `assets/scss/wdt/` |
| Template overrides | `templates/chrome/` |
| UI Kernel collector | `src/DataCollector/UiKernelDataCollector.php` |
| Settings dialog + shell JS | `assets/js/shell.js`, Stimulus controllers |

## After upgrading Symfony

1. Bump `symfony/web-profiler-bundle` in your project
2. Run [Verification](verification.md) WDT smoke checks
3. If a stock collector panel looks unstyled, check whether upstream changed collector Twig — open an issue with Symfony version and panel name

## See also

- [Third-party collectors](third-party-collectors.md)
- [CHANGELOG](../CHANGELOG.md)
