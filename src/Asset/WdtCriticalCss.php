<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Asset;

/**
 * First-paint WDT skeleton rules — inline in toolbar_js only (051 ZOC-3).
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
    position: absolute;
    right: 0;
    top: 1px;
    width: 36px;
    height: 35px;
    border: 0;
    background: transparent;
    cursor: pointer;
}
CSS;

    private function __construct()
    {
    }
}
