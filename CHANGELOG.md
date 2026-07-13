# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.0] - 2026-07-13

### Added

- Greenfield Web Profiler toolbar and profiler shell styled with UI Kernel theme tokens and ux-blocks layout components
- Flex recipe `symfinity/ui-profiler` 0.1 — bundle registration for `dev` and `test`, default `config/packages/symfinity_ui_profiler.yaml`
- UI Kernel collector panel (`?panel=ui_kernel`) for active theme id, CSS cascade layers, and token summary
- Reskinned stock Symfony collector panels (request, time, twig, doctrine, security, form, cache, and related bundles)
- Owned Sass author pipeline for shell and WDT CSS (`assets/scss/` compiled to committed `assets/styles/`)
- Uniform typography in collector tables via shared shell CSS
- Panel heading structure: one page title (`h2`) per panel, `h3` for subsections
- Stimulus controllers for profiler settings dialog, tabs, and table search
- Handbook: installation, quickstart, configuration, usage, verification, third-party collectors, upgrade
- Split mirror metadata: MIT LICENSE, CONTRIBUTING, SECURITY policy, PHPUnit + PHPStan CI workflow

### Notes

- **Dev/test only** — do not enable in production
- Requires `symfinity/ui-kernel` ^0.3.1, `symfinity/ux-blocks-core` ^0.1, `symfinity/ux-blocks-extended` ^0.1, and `symfony/web-profiler-bundle`
- Symfony 7.4 or 8.x, PHP 8.2+
- Unmigrated third-party collector templates may still emit legacy `sf-*` markup; owned shell, WDT, and stock collector chrome do not
