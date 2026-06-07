# symfinity/ui-profiler — maintainer quickstart (051)

Greenfield Chameleon Web Profiler — **zero owned `.css` files**, UI Kernel tokens, ux-blocks markup.

## QA (package)

```bash
cd src/symfinity

# Parity gate (P0)
docker compose --env-file .env.docker run --rm -T -w /app php \
  php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist --group ui-profiler-parity

# Unit gate
docker compose --env-file .env.docker run --rm -T -w /app php \
  php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist --testsuite default

# Optional Stimulus path (DI-T2)
docker compose --env-file .env.docker run --rm -T -w /app php \
  php vendor/bin/phpunit -c packages/ui-profiler/phpunit.xml.dist --group ui-profiler-runtime
```

## Dogfood (post GC-6)

Chameleon showcase overlay ships `symfinity/ui-profiler` + `UiProfilerBundle`:

```bash
# From org L0 — recreate or composer update existing sandbox
make dogfood-new SLUG=chameleon-showcase VERSION='7.4.*'
make dogfood-serve SLUG=chameleon-showcase
```

| URL | Check |
|-----|-------|
| `/` | WDT stable; no layout jump |
| `/_profiler/latest` | `data-ui-profiler-shell`; ux-blocks sidebar |
| `/_profiler/latest?panel=request` | Request collector CO-01 |
| `/_profiler/latest?panel=time` | Timeline CO-02 |

## Keyboard map

| Key | Action |
|-----|--------|
| `/` | Focus profiler table search (when not in an input) |
| `Esc` | Close settings dialog |

## Architecture

| Layer | Source |
|-------|--------|
| L0 look | `ui_kernel_css()` |
| L1 markup | ux-blocks-core + ux-blocks-extended |
| WDT skeleton | Inline Twig/PHP (ZOC-*) — no `assets/**/*.css` |
| L3 native | `assets/profiler/shell.js`, `assets/wdt/index.js` |
| L3 optional | `assets/controllers/ui-profiler/*` when StimulusBundle registered |

Archive inspiration (read-only): `packages/temp/ui-profiler/`.

Spec: [51-ui-profiler-reboot](../../../../specs/symfinity/symfinity/51-ui-profiler-reboot/spec.md)

## Troubleshooting

| Symptom | Fix |
|---------|-----|
| Toolbar FOUC / height jump | Check inline WDT skeleton in `toolbar_js.html.twig` |
| Empty `/_wdt/styles` | Verify `toolbar.css.twig` + `WdtToolbarStylesheet` PHP embed |
| Unstyled third-party collector | Migrate collector Twig to ux-blocks — no stock CSS copy |
| Dogfood broken assets | Delete `var/dogfood/{slug}/public/assets/` (AssetMapper dev rule) |
| `CssBudgetTest` fails | Remove owned `.css` — use Kernel tokens or ux-blocks |

Upstream pin: [upstream-sync.md](./upstream-sync.md)
