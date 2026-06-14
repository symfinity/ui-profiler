<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Asset;

/**
 * WDT toolbar rules for /_wdt/styles — embedded in PHP (051 ZOC: no owned .css files).
 */
final class WdtToolbarStylesheet
{
    public const CSS = <<<'CSS'
[data-ui-profiler-wdt].sf-toolbarreset {
    --ui-toolbar-sans: var(--ui-font-family-sans);
    --ui-toolbar-bg: var(--ui-color-surface);
    --ui-toolbar-fg: var(--ui-color-text);
    --ui-toolbar-muted: var(--ui-color-text-muted);
    --ui-toolbar-icon: var(--ui-color-text);
    --ui-toolbar-hover: var(--ui-color-surface-elevated);
    --ui-toolbar-link: var(--ui-color-primary);
    --ui-toolbar-red: var(--ui-color-danger);
    --ui-toolbar-red-fg: var(--ui-color-surface);
    --ui-toolbar-yellow: var(--ui-color-warning);
    --ui-toolbar-yellow-fg: var(--ui-color-surface);
    --ui-toolbar-green: var(--ui-color-success);
    background: var(--ui-toolbar-bg);
    bottom: 0;
    box-shadow: inset 0 1px 0 color-mix(in srgb, var(--ui-color-text) 12%, var(--ui-color-surface)), 0 -1px 0 color-mix(in srgb, var(--ui-color-text) 25%, transparent);
    color: var(--ui-toolbar-fg);
    font: 0.75rem var(--ui-toolbar-sans);
    left: 0;
    margin: 0;
    padding: 0 36px 0 0;
    position: fixed;
    right: 0;
    text-align: left;
    z-index: 99999;
    user-select: none;
    -webkit-user-select: none;
}
.sf-toolbarreset *:not(svg rect) { box-sizing: content-box; letter-spacing: normal; vertical-align: baseline; width: auto; }
.sf-toolbar-clearer { clear: both; height: 36px; }
.sf-toolbarreset abbr { border-bottom: 1px dashed var(--ui-toolbar-muted); }
.sf-toolbarreset svg, .sf-toolbarreset img { display: inline-block; height: 20px; vertical-align: middle; width: 20px; }
.sf-toolbarreset .sf-toolbar-icon svg { color: inherit; }
.sf-toolbarreset .sf-toolbar-toggle-button {
    -webkit-appearance: none;
    appearance: none;
    background: var(--ui-toolbar-bg);
    border: 0;
    border-radius: 0;
    color: var(--ui-toolbar-icon);
    cursor: pointer;
    height: 35px;
    margin: 0;
    outline: none;
    padding: 0;
    position: absolute;
    right: 0;
    text-align: center;
    top: 1px;
    width: 36px;
}
.sf-toolbarreset .sf-toolbar-toggle-button:hover { background: var(--ui-toolbar-hover); }
.sf-toolbar .sf-toolbar-toggle-button i { display: grid; font-style: normal; height: 35px; place-content: center; width: 100%; }
.sf-toolbar-toggle-button .sf-toolbar-icon-closed svg { display: block; }
.sf-toolbar.sf-toolbar-closed { min-height: 0; }
.sf-toolbar.sf-toolbar-closed .sf-toolbar-clearer { display: none; }
.sf-toolbar.sf-toolbar-closed .sf-toolbarreset { background: transparent; box-shadow: none; height: 0; min-height: 0; overflow: visible; padding: 0; }
.sf-toolbar.sf-toolbar-closed .sf-toolbarreset .sf-toolbar-block { display: none; }
.sf-toolbar.sf-toolbar-closed .sf-toolbarreset .sf-toolbar-toggle-button {
    background: var(--ui-toolbar-hover);
    border-top: 2px solid var(--ui-toolbar-hover);
    box-shadow: inset 1px 0 0 color-mix(in srgb, var(--ui-color-text) 12%, var(--ui-color-surface)), 0 -1px 0 color-mix(in srgb, var(--ui-color-text) 25%, transparent);
    top: -37px;
}
.sf-toolbar.sf-toolbar-opened .sf-toolbar-toggle-button .sf-toolbar-icon-closed,
.sf-toolbar.sf-toolbar-closed .sf-toolbar-toggle-button .sf-toolbar-icon-opened { display: none; }
.sf-toolbar.sf-toolbar-opened .sf-toolbar-toggle-button .sf-toolbar-icon-opened::before { content: "\00d7"; display: block; font-size: 1.25rem; font-style: normal; line-height: 1; }
.sf-toolbar-block { cursor: default; display: block; float: left; height: 36px; max-width: 25%; position: relative; white-space: nowrap; }
.sf-toolbar-block > a, .sf-toolbar-block > a:hover { background: transparent; color: inherit; display: block; text-decoration: none; }
.sf-toolbar-block .sf-toolbar-icon { align-items: center; color: var(--ui-toolbar-icon); display: flex; height: 36px; overflow: hidden; padding: 0 7px; text-overflow: ellipsis; }
.sf-toolbar-block .sf-toolbar-value { color: var(--ui-toolbar-fg); font-size: 0.75rem; line-height: 36px; }
.sf-toolbar-block .sf-toolbar-label, .sf-toolbar-block .sf-toolbar-class-separator { color: var(--ui-toolbar-muted); font-size: 0.75rem; margin-left: 2px; }
.sf-toolbar-block .sf-toolbar-status { background: var(--ui-toolbar-hover); color: var(--ui-color-surface); display: inline-block; min-width: 15px; padding: 3px 6px; text-align: center; }
.sf-toolbar-block .sf-toolbar-status.sf-toolbar-status-green { background: var(--ui-toolbar-green); }
.sf-toolbar-block .sf-toolbar-status.sf-toolbar-status-red,
.sf-toolbar-block .sf-toolbar-info .sf-toolbar-status.sf-toolbar-status-red { background: var(--ui-toolbar-red); color: var(--ui-toolbar-red-fg); }
.sf-toolbar-block .sf-toolbar-status.sf-toolbar-status-yellow,
.sf-toolbar-block .sf-toolbar-info .sf-toolbar-status.sf-toolbar-status-yellow { background: var(--ui-toolbar-yellow); color: var(--ui-toolbar-yellow-fg); }
.sf-toolbar-block.sf-toolbar-status-green::before,
.sf-toolbar-block.sf-toolbar-status-red::before,
.sf-toolbar-block.sf-toolbar-status-yellow::before {
    background: var(--ui-toolbar-yellow);
    border-radius: 6px;
    bottom: 1px;
    content: '';
    height: 3px;
    left: 0;
    position: absolute;
    width: 98%;
    z-index: 10005;
}
.sf-toolbar-block.sf-toolbar-status-red::before { background: var(--ui-toolbar-red); }
.sf-toolbar-block.sf-toolbar-status-green::before { background: var(--ui-toolbar-green); }
.sf-toolbar-block-request.sf-toolbar-block.sf-toolbar-status-green::before,
.sf-toolbar-block-request.sf-toolbar-block.sf-toolbar-status-red::before,
.sf-toolbar-block-request.sf-toolbar-block.sf-toolbar-status-yellow::before { display: none; }
.sf-toolbar-block:hover, .sf-toolbar-block.hover { position: relative; }
.sf-toolbar-block:hover .sf-toolbar-icon, .sf-toolbar-block.hover .sf-toolbar-icon {
    background: var(--ui-toolbar-hover);
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
    box-shadow: 1px 0 0 color-mix(in srgb, var(--ui-color-text) 12%, var(--ui-color-surface)), inset 0 -1px 0 color-mix(in srgb, var(--ui-color-text) 12%, var(--ui-color-surface));
    position: relative;
    z-index: 10002;
}
.sf-toolbar-block.sf-toolbar-block-right:hover .sf-toolbar-icon { box-shadow: -1px 0 0 color-mix(in srgb, var(--ui-color-text) 12%, var(--ui-color-surface)), inset 0 -1px 0 color-mix(in srgb, var(--ui-color-text) 12%, var(--ui-color-surface)); }
.sf-toolbar-block .sf-toolbar-info {
    background: var(--ui-toolbar-hover);
    border-radius: 4px 4px 0 0;
    bottom: 36px;
    color: var(--ui-toolbar-fg);
    display: none;
    padding: 9px 0;
    position: absolute;
    z-index: 100000;
}
.sf-toolbar-block:hover .sf-toolbar-info, .sf-toolbar-block.hover .sf-toolbar-info {
    display: block;
    max-height: 480px;
    width: max-content;
    max-width: calc(100vw - 16px);
    overflow: hidden auto;
    padding: 10px;
    word-wrap: break-word;
}
.sf-toolbar-block .sf-toolbar-info:empty { visibility: hidden; }
.sf-toolbar-block hr { border-top: 1px solid var(--ui-toolbar-muted); margin: 4px 0; padding-top: 4px; }
.sf-toolbar-block .sf-toolbar-info-group { border-bottom: 1px solid color-mix(in srgb, var(--ui-color-text) 20%, var(--ui-color-surface)); margin-bottom: 4px; padding-bottom: 2px; }
.sf-toolbar-block .sf-toolbar-info-group:last-child { border-bottom: 0; margin-bottom: 0; padding-bottom: 0; }
.sf-toolbar-block .sf-toolbar-info-piece { border-bottom: 3px solid transparent; display: table-row; }
.sf-toolbar-block .sf-toolbar-info-piece:has(table) { border-bottom: 0; display: block; }
.sf-toolbar-block .sf-toolbar-info-piece b { color: var(--ui-toolbar-muted); display: table-cell; padding: 4px 8px 4px 0; }
.sf-toolbar-block .sf-toolbar-info-piece span { color: var(--ui-toolbar-fg); }
.sf-toolbar-block .sf-toolbar-info table { border-collapse: collapse; border-spacing: 0; }
.sf-toolbar-block .sf-toolbar-info table:not(.sf-toolbar-ajax-requests) th,
.sf-toolbar-block .sf-toolbar-info table:not(.sf-toolbar-ajax-requests) td { padding: 0; }
.sf-toolbar-block .sf-toolbar-info-piece-additional, .sf-toolbar-block .sf-toolbar-info-piece-additional-detail { display: none; }
div.sf-toolbar .sf-toolbar-block a:hover { text-decoration: none; }
div.sf-toolbar .sf-toolbar-block .sf-toolbar-info-piece a { color: var(--ui-toolbar-link); text-decoration: underline; }
.sf-toolbar-block-request .sf-toolbar-icon { padding: 0 4px; }
.sf-toolbar-block-request .sf-toolbar-status { border-radius: 6px; flex-shrink: 0; font-size: 0.75rem; font-weight: 500; padding: 4px 8px; }
.sf-toolbar-block-request .sf-toolbar-label,
.sf-toolbar-block-request .sf-toolbar-value,
.sf-toolbar-block-request .sf-toolbar-request-icon { flex-shrink: 0; }
.sf-toolbar-block-ajax .sf-toolbar-icon { cursor: pointer; }
.sf-toolbar-block-ajax:hover .sf-toolbar-info,
.sf-toolbar-block-ajax.hover .sf-toolbar-info { min-width: min(32rem, calc(100vw - 16px)); }
.sf-toolbar-block .sf-toolbar-info-piece b.sf-toolbar-ajax-info {
    color: var(--ui-toolbar-fg);
    display: block;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 6px;
}
.sf-toolbar-block .sf-toolbar-info-piece:has(.sf-toolbar-ajax-requests) { display: block; width: 100%; }
.sf-toolbar-block .sf-toolbar-info table.sf-toolbar-ajax-requests {
    border: 1px solid var(--ui-toolbar-muted);
    border-collapse: separate;
    border-radius: 4px;
    border-spacing: 0;
    font-family: var(--ui-toolbar-sans);
    font-variant: tabular-nums;
    margin: 0;
    overflow: hidden;
    width: 100%;
}
.sf-toolbar-block .sf-toolbar-info table.sf-toolbar-ajax-requests thead th {
    background: var(--ui-toolbar-bg);
    border: 0;
    border-bottom: 1px solid var(--ui-toolbar-muted);
    color: var(--ui-toolbar-muted);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.04em;
    padding: 6px 8px;
    text-align: left;
    text-transform: uppercase;
    vertical-align: middle;
    white-space: nowrap;
}
.sf-toolbar-block .sf-toolbar-info table.sf-toolbar-ajax-requests tbody td {
    background: var(--ui-toolbar-hover);
    border: 0;
    border-bottom: 1px solid color-mix(in srgb, var(--ui-toolbar-muted) 35%, transparent);
    color: var(--ui-toolbar-fg);
    font-size: 11px;
    padding: 5px 8px;
    vertical-align: middle;
}
.sf-toolbar-block .sf-toolbar-info table.sf-toolbar-ajax-requests tbody tr:last-child td { border-bottom: 0; }
.sf-toolbar-block .sf-toolbar-info table.sf-toolbar-ajax-requests tbody tr:nth-child(even) td {
    background: color-mix(in srgb, var(--ui-toolbar-bg) 50%, var(--ui-toolbar-hover));
}
.sf-toolbar-block .sf-toolbar-info table.sf-toolbar-ajax-requests tbody tr.sf-ajax-request-loading td {
    animation: ui-profiler-wdt-blink 0.5s ease-in-out infinite;
}
.sf-toolbar-block .sf-toolbar-info table.sf-toolbar-ajax-requests tbody tr.sf-ajax-request-error td {
    background: color-mix(in srgb, var(--ui-toolbar-red) 12%, var(--ui-toolbar-hover));
}
.sf-toolbar-block .sf-toolbar-info table.sf-toolbar-ajax-requests td:nth-child(2) a,
.sf-toolbar-block .sf-toolbar-info table.sf-toolbar-ajax-requests td:nth-child(2) {
    font-family: var(--ui-toolbar-sans);
    font-size: 11px;
}
.sf-toolbar-block .sf-toolbar-info table.sf-toolbar-ajax-requests td:nth-child(3) {
    font-weight: 600;
    white-space: nowrap;
}
.sf-toolbar-block .sf-toolbar-info table.sf-toolbar-ajax-requests .sf-toolbar-status {
    border-radius: 4px;
    display: inline-block;
    font-size: 10px;
    font-weight: 600;
    line-height: 1.2;
    min-width: 2rem;
    padding: 2px 6px;
    text-align: center;
}
.sf-toolbar-ajax-requests .sf-ajax-request-url a { color: var(--ui-toolbar-link); text-decoration: none; }
.sf-toolbar-ajax-requests .sf-ajax-request-url a:hover { text-decoration: underline; }
.sf-ajax-request-url { line-height: 1.25; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sf-ajax-request-duration { text-align: right; }
.sf-ajax-request-loading { animation: ui-profiler-wdt-blink 0.5s ease-in-out infinite; }
@keyframes ui-profiler-wdt-blink {
    0%, 100% { background: var(--ui-toolbar-bg); }
    50% { background: var(--ui-toolbar-hover); }
}
.sf-toolbar-block.sf-toolbar-block-dump .sf-toolbar-info { box-sizing: border-box; left: 0; max-width: none; position: fixed; width: 100%; }
.sf-toolbar-block-dump pre.sf-dump { background: var(--ui-toolbar-bg); border-color: var(--ui-toolbar-muted); border-radius: 0; margin: 6px 0 12px; }
.sf-toolbar-block-dump .sf-toolbar-info img { display: none; }
.sf-toolbar-icon .sf-toolbar-label, .sf-toolbar-icon .sf-toolbar-value { display: none; }
.sf-toolbar-block-config .sf-toolbar-icon .sf-toolbar-label, .sf-cli .sf-toolbar-icon .sf-toolbar-label { display: inline-flex; align-items: center; margin-left: 0; }
.sf-toolbar-block .sf-toolbar-icon img + span,
.sf-toolbar-block .sf-toolbar-icon svg + span { margin-left: 4px; }
.sf-toolbar-block-config .sf-toolbar-icon .sf-toolbar-value,
.sf-toolbar-block.sf-toolbar-block-sf-cli .sf-toolbar-value { line-height: 1; margin-left: 5px; }
.sf-toolbar-block-config svg path,
.sf-toolbar-block-config svg .sf-svg-path { fill: currentColor; }
@media (min-width: 768px) {
    .sf-toolbar-icon .sf-toolbar-label, .sf-toolbar-icon .sf-toolbar-value { display: inline; }
    .sf-toolbar-block-config .sf-toolbar-icon .sf-toolbar-label { display: inline-flex; align-items: center; }
    .sf-toolbar-block .sf-toolbar-icon { padding: 0 10px; }
    .sf-toolbar-block-time .sf-toolbar-icon svg, .sf-toolbar-block-memory .sf-toolbar-icon svg { display: none; }
    .sf-toolbar-block-time .sf-toolbar-icon svg + span, .sf-toolbar-block-memory .sf-toolbar-icon svg + span { margin-left: 0; }
    .sf-toolbar-block-request:hover .sf-toolbar-info { max-width: calc(100vw - 16px); width: max-content; }
    .sf-toolbar-block-right { float: right; margin: 0; }
}
@media (min-width: 1024px) {
    .sf-toolbar-block .sf-toolbar-info-piece-additional, .sf-toolbar-block .sf-toolbar-info-piece-additional-detail { display: inline; }
    .sf-toolbar-block .sf-toolbar-info-piece-additional:empty, .sf-toolbar-block .sf-toolbar-info-piece-additional-detail:empty { display: none; }
}
.sf-error-toolbar .sf-toolbarreset { font: 13px/36px var(--ui-toolbar-sans); height: 36px; padding: 0 15px; }
.sf-error-toolbar .sf-toolbarreset a { color: var(--ui-toolbar-link); margin-left: 5px; text-decoration: underline; }
.sf-error-toolbar .sf-toolbarreset a:hover { text-decoration: none; }
@media print { .sf-toolbar { display: none !important; } }
[data-ui-profiler-wdt].ui-profiler-wdt :where(a.ui-profiler-wdt__link) { color: var(--ui-toolbar-link) !important; text-decoration: none !important; }
[data-ui-profiler-wdt].ui-profiler-wdt :where(a.ui-profiler-wdt__link:hover) { color: color-mix(in srgb, var(--ui-color-info) 80%, var(--ui-color-surface)) !important; }
CSS;

    private function __construct()
    {
    }
}
