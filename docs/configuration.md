# Configuration

Configuration root: **`symfinity_ui_profiler`**.

Default file: `config/packages/symfinity_ui_profiler.yaml` (copied by Flex recipe).

## Options

| Key | Default | Description |
|-----|---------|-------------|
| `enabled` | `true` | Master switch for profiler chrome replacement |
| `wdt.critical_css_inline` | `true` | Inline critical WDT CSS to avoid layout jump |
| `wdt.module_preload` | `true` | Emit modulepreload hints for WDT assets |

## Example

```yaml
when@dev:
    symfinity_ui_profiler:
        enabled: true
        wdt:
            critical_css_inline: true
            module_preload: true
```

## Environment scope

Package defaults use `when@dev` and `when@test` — do **not** register the bundle in **prod**.

## UI Kernel dependency

Profiler chrome reads UI Kernel theme tokens. Configure UI Kernel separately under `symfinity_ui_kernel`.

## See also

- [Installation](installation.md)
- [Quick start](quickstart.md)
