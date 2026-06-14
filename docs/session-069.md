# Implement session 069 — ui-profiler stabilization

**Planning label:** SYMFINITY-69  
**Started:** 2026-06-14

## Bug triage (BugTriageItem)

| ID | Repro | Severity | Status | Verification |
|----|-------|----------|--------|--------------|
| — | Full PHPUnit gate | — | **pass** | 82/82 (2026-06-14) |
| — | `@group ui-profiler-parity` | — | **pass** | 53/53 |
| — | Manual `ui-lab` matrix | — | **pending sign-off** | Maintainer browser smoke per quickstart |
| BUG-069-01 | Settings dialog layout/chrome | P1 | **fixed** | ShellChromeStylesheet — dialog header, picker active state, backdrop |
| BUG-069-02 | Profiler shell tables/cards unstyled | P1 | **fixed** | ShellChromeStylesheet — table layout, card shadow, collector padding |
| BUG-069-03 | WDT AJAX requests table cramped | P1 | **fixed** | WdtToolbarStylesheet — cell padding, thead/body contrast |

No P0/P1 bugs found in automated gate at session start. Manual ui-lab triage deferred to exit checklist (T026).

## Visual polish inventory status

| ID | Surface | Priority | Status |
|----|---------|----------|--------|
| POL-01 | WDT toolbar bar + badges | P0 | done |
| POL-02 | Profiler shell grid + sidebar | P0 | done |
| POL-03 | Settings `<dialog>` (theme/width) | P0 | done |
| POL-04 | Shell header / collector tabs | P1 | done |
| POL-05 | Table + `/` search (stock panels) | P1 | done |
| POL-06 | Timeline / time panel | P1 | done |
| POL-07 | Exception panel | P1 | done |
| POL-08 | Form / validator panels | P2 | deferred |
| POL-09 | Security panel | P2 | deferred |
| POL-10 | UI Kernel collector panel | P0 | done |

ZOC gate green after baseline: `GreenfieldMarkupTest|CssBudgetTest|AssetOwnershipTest` 8/8.

## Distribution notes

- `mono recipes:validate`: **6/6 valid** including `symfinity/ui-profiler` 0.1 (2026-06-14).
- `release:publish 0.1.0 --dry-run`: **No packages to release** (no git diff vs base in current tree).
- Public tag **blocked:r2** until R2 Chameleon packages on Packagist @ `^0.1`.
