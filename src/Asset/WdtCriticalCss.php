<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Asset;

/**
 * First-paint WDT skeleton rules — inline in toolbar_js only (051 ZOC-3).
 *
 * This is a deliberate minimal subset of {@see WdtToolbarStylesheet} (the canonical
 * full ruleset served at /_wdt/styles). Some selectors here (e.g.
 * .sf-toolbar-toggle-button and the closed-state rules) are intentionally redefined
 * with placeholder/transparent values so the toolbar does not flash before the full
 * stylesheet loads. Keep the two in sync when changing toggle-button geometry.
 */
final class WdtCriticalCss
{
    public const CSS = <<<'CSS'
[data-ui-profiler-wdt].sf-toolbarreset {
    position:fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 99999;
    background:var(--ui-color-surface);
    color: var(--ui-color-text, #eee);
    font: 11px var(--ui-font-family-sans, system-ui, sans-serif);
    min-height: 36px;
    box-sizing: border-box;
}
.sf-toolbar-clearer { clear: both; height: 36px; }
.sf-toolbarreset .sf-toolbar-toggle-button {
    -webkit-appearance: none;
    appearance: none;
    position: absolute;
    right: 0;
    top: 1px;
    width: 36px;
    height: 35px;
    border: 0;
    border-radius: 0;
    background: transparent;
    cursor: pointer;
}
.sf-toolbar .sf-toolbar-toggle-button i { font-style: normal; }
.sf-toolbar.sf-toolbar-closed .sf-toolbarreset .sf-toolbar-toggle-button {
    background: var(--ui-color-surface-elevated, #242424);
    border-top: 2px solid var(--ui-color-surface-elevated, #242424);
    top: -37px;
}
CSS;

    private function __construct()
    {
    }
}
