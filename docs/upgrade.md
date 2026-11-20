# Upgrade

Patch releases follow [CHANGELOG](../CHANGELOG.md).

## 0.1.2

The Doctrine toolbar loads when the second-level cache is off. The cache-off badge reads "disabled". No config change.

```bash
composer update symfinity/ui-profiler
```

## Before upgrading

1. Confirm `symfinity/ui-kernel`, `symfinity/ux-blocks-core`, and `symfinity/ux-blocks-extended` resolve at the versions declared in `composer.json`.
2. Re-run [Verification](verification.md) WDT smoke after bumping.

## Breaking changes

None in the `0.1.x` line for consumer Flex installs.

## See also

- [Installation](installation.md)
- [Configuration](configuration.md)
