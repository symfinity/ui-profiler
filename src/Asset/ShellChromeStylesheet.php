<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Asset;

/**
 * Profiler shell chrome — embedded in PHP (051 ZOC: no owned .css files).
 * Scoped to [data-ui-profiler-shell] and data-ui-profiler-* hooks.
 */
final class ShellChromeStylesheet
{
    public const CSS = <<<'CSS'
/* Shell chrome (L2) — UI Kernel grid + ux-blocks gaps + settings dialog. symfinity 044 css-strangler. */

body {
    background-color: var(--ui-color-surface);
}

[data-ui-profiler-shell] {
    font-family: var(--ui-font-family-sans, system-ui, sans-serif);
    background-color: var(--ui-color-surface);
    color: var(--ui-color-text);
    font-size: var(--font-size-body);
    line-height: 1.4;
    min-height: 100%;
    --ui-color-primary-subtle: color-mix(in srgb, var(--ui-color-primary) 12%, var(--ui-color-surface));
    --font-family-system: var(--ui-font-family-sans);
    --font-family-monospace: var(--ui-font-family-mono);
    --font-size-body: var(--ui-font-size-md);
    --font-size-monospace: var(--ui-font-size-sm);
    --font-variant-ligatures-monospace: none;
    --table-font-size: var(--ui-font-size-sm);
    --h2-border-color: var(--ui-color-border);
    --heading-code-background: var(--ui-color-surface-elevated);
    --form-input-border-color: var(--ui-color-border);
    --menu-border-color: var(--ui-color-border);
    --menu-color: var(--ui-color-text);
    --menu-disabled-color: var(--ui-color-text-muted);
    --menu-icon-color: var(--ui-color-text-muted);
    --menu-icon-disabled-color: var(--ui-color-border);
    --menu-active-color: var(--ui-color-primary);
    --menu-active-marker-background: var(--ui-color-primary);
    --menu-active-background: var(--ui-color-surface-elevated);
    --tab-background: var(--ui-color-surface-elevated);
    --tab-border-color: var(--ui-color-border);
    --tab-active-border-color: var(--ui-color-border);
    --tab-active-background: var(--ui-color-surface);
    --tab-active-color: var(--ui-color-text);
    --tab-disabled-color: var(--ui-color-text-muted);
    --table-border-color: var(--ui-color-border);
    --table-header: var(--ui-color-surface-elevated);
    --table-header-border-color: var(--ui-color-border);
    --border: 1px solid var(--ui-color-border);
    --terminal-bg: var(--ui-color-text);
    --terminal-border-color: var(--ui-color-border);
    --terminal-warning-color: var(--ui-color-warning);
    --terminal-warning-bg: var(--ui-color-warning);
    --terminal-error-color: var(--ui-color-danger);
    --terminal-error-bg: var(--ui-color-danger);
    --highlight-string: var(--ui-color-success);
    --highlight-constant: var(--ui-color-primary);
    --highlight-selected-line: color-mix(in srgb, var(--ui-color-warning) 35%, transparent);
    --base-1: var(--ui-color-surface-elevated);
    --base-5: var(--ui-color-text);
    --summary-status-border-width: 6px;
    --page-background: var(--ui-color-surface);
    --color-link: var(--ui-color-primary);
    --header-background: var(--ui-color-surface-elevated);
    --header-border-color: var(--ui-color-border);
    --header-status-request-method-color: var(--ui-color-text-muted);
    --header-metadata-key: var(--ui-color-text-muted);
    --header-metadata-value: var(--ui-color-text);
    --header-success-background: color-mix(in srgb, var(--ui-color-success) 12%, var(--ui-color-surface));
    --header-success-border-color: var(--ui-color-success);
    --header-success-box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--ui-color-success) 22%, var(--ui-color-surface));
    --header-success-title-color: var(--ui-color-text);
    --header-success-status-code-background: var(--ui-color-success);
    --header-success-status-code-color: var(--ui-color-surface);
    --header-success-status-text-color: var(--ui-color-success);
    --header-warning-background: color-mix(in srgb, var(--ui-color-warning) 12%, var(--ui-color-surface));
    --header-warning-border-color: var(--ui-color-warning);
    --header-warning-box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--ui-color-warning) 22%, var(--ui-color-surface));
    --header-warning-title-color: var(--ui-color-text);
    --header-warning-status-code-background: color-mix(in srgb, var(--ui-color-warning) 35%, var(--ui-color-surface));
    --header-warning-status-code-color: var(--ui-color-text);
    --header-warning-status-text-color: var(--ui-color-warning);
    --header-error-background: color-mix(in srgb, var(--ui-color-danger) 12%, var(--ui-color-surface));
    --header-error-border-color: var(--ui-color-danger);
    --header-error-box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--ui-color-danger) 22%, var(--ui-color-surface));
    --header-error-title-color: var(--ui-color-text);
    --header-error-status-code-background: transparent;
    --header-error-status-code-color: var(--ui-color-danger);
    --header-error-status-text-color: var(--ui-color-danger);
}
[data-ui-profiler-shell][data-ui-role="grid-container"] {
    box-sizing: border-box;
    margin-inline: auto;
    max-width: 75rem;
    padding-inline: var(--ui-grid-gap, 1rem);
    width: 100%;
}

body.width-full [data-ui-profiler-shell][data-ui-role="grid-container"],
[data-ui-profiler-shell].width-full[data-ui-role="grid-container"] {
    margin-inline: 0;
    max-width: none;
}


#sidebar {
    margin-bottom: 1.875rem;
    position: sticky;
    top: 1.25rem;
    z-index: 9999;
}

#sidebar-contents [data-ui-role="link"] {
    text-decoration: none;
}

#collector-wrapper {
    min-width: 0;
}

#collector-wrapper [data-ui-role="card"] {
    min-height: 60vh;
}


/* Settings modal — native <dialog> + kernel overlay */
[data-ui-profiler-settings-trigger] {
    align-items: center;
    color: var(--ui-color-text-muted);
    display: flex;
    margin: 0.625rem 0 0.3125rem;
}

[data-ui-profiler-settings-trigger] svg {
    flex-shrink: 0;
    height: 1.125rem;
    margin-right: 0.25rem;
    width: 1.125rem;
}

#profiler-settings[data-ui-role="dialog"] {
    border: 1px solid var(--ui-color-border);
    border-radius: var(--ui-radius-md, 8px);
    box-shadow: var(--ui-shadow-lg);
    margin: auto;
    max-width: min(37.5rem, 94vw);
    padding: var(--ui-space-lg, 1.25rem);
    width: min(37.5rem, 94vw);
}

#profiler-settings[data-ui-role="dialog"]::backdrop {
    background: var(--ui-backdrop-color, color-mix(in srgb, var(--ui-color-text) 35%, transparent));
}

#profiler-settings[data-ui-role="dialog"] > [data-ui-role="stack"] > header {
    align-items: center;
    justify-content: space-between;
}

#profiler-settings[data-ui-role="dialog"] > [data-ui-role="stack"] > header h3 {
    font-size: 1.125rem;
    font-weight: 600;
    line-height: 1.2;
    margin: 0;
}

[data-ui-profiler-settings-picker] {
    border: 1px solid color-mix(in srgb, var(--ui-color-text-muted) 70%, var(--ui-color-border));
    border-radius: var(--ui-radius-sm, 4px);
    display: flex;
    flex-direction: row;
    overflow: hidden;
}

[data-ui-profiler-settings-picker] > [data-ui-role="button"],
[data-ui-profiler-settings-picker] > label[data-ui-role="button"] {
    align-items: center;
    border-radius: 0;
    display: flex;
    flex: 1 1 0;
    flex-direction: column;
    gap: 0.375rem;
    justify-content: center;
    min-height: 5.5rem;
    min-width: 0;
    text-align: center;
    text-decoration: none;
}

[data-ui-profiler-settings-picker] > [data-ui-role="button"] + [data-ui-role="button"],
[data-ui-profiler-settings-picker] > label[data-ui-role="button"] + label[data-ui-role="button"] {
    border-left: 1px solid color-mix(in srgb, var(--ui-color-text-muted) 70%, var(--ui-color-border));
}

[data-ui-profiler-settings-picker] svg {
    color: var(--ui-color-text-muted);
    display: block;
    flex-shrink: 0;
    height: 2.25rem;
    pointer-events: none;
    width: 2.25rem;
}

[data-ui-profiler-settings-picker] [data-ui-variant="primary"] svg {
    color: var(--ui-color-primary);
}

[data-ui-profiler-settings-picker] > [data-ui-variant="ghost"] {
    color: var(--ui-color-text);
}

[data-ui-profiler-settings-picker] > [data-ui-variant="ghost"] svg {
    color: var(--ui-color-text-muted);
}

@media (max-width: 768px) {
    [data-ui-profiler-settings-trigger] {
        color: transparent;
    }

    #sidebar:hover [data-ui-profiler-settings-trigger],
    #sidebar.expanded [data-ui-profiler-settings-trigger] {
        color: var(--ui-color-text-muted);
    }

    #sidebar {
        flex-basis: 50px;
        overflow-x: hidden;
        transition: flex-basis 200ms ease-out;
    }

    #sidebar:hover,
    #sidebar.expanded {
        flex-basis: 220px;
    }

    #sidebar-search {
        display: none;
    }

    #sidebar:hover #sidebar-search.sf-toggle-visible,
    #sidebar.expanded #sidebar-search.sf-toggle-visible {
        display: block;
    }

    #sidebar .module {
        display: none;
    }

    #sidebar:hover .module,
    #sidebar.expanded .module {
        display: block;
    }

    #sidebar:not(:hover):not(.expanded) [data-ui-profiler-menu-label] [data-ui-role="badge"] {
        border-radius: 50%;
        border: 1px solid #eee;
        height: 8px;
        min-width: 0;
        padding: 0;
        right: 4px;
        text-indent: -9999px;
        top: 50%;
        width: 8px;
    }

    .visible-small {
        display: inherit;
    }
    .hidden-small {
        display: none;
    }

    .btn-sm svg {
        margin-left: 2px;
    }
}



.visually-hidden {
    border: 0 !important;
    clip: rect(0, 0, 0, 0) !important;
    height: 1px !important;
    margin: -1px !important;
    overflow: hidden !important;
    padding: 0 !important;
    position: absolute !important;
    width: 1px !important;
    white-space: nowrap !important;
}

html {
    overflow-y: scroll;
}

#collector-content h2 {
    font-weight: 500;
    margin: 0 0 0.5em;
}
#collector-content h3,
#collector-content h4 {
    font-weight: 500;
    margin: 1.5em 0 0.5em;
}

#collector-content [data-ui-role="table"] tbody th,
#collector-content [data-ui-role="table"] tbody td {
    font-family: var(--font-family-monospace);
    font-size: var(--font-size-monospace);
    font-variant-ligatures: var(--font-variant-ligatures-monospace);
}

#collector-content [data-ui-role="table"] tbody th.font-normal,
#collector-content [data-ui-role="table"] tbody td.font-normal {
    font-family: var(--font-family-system);
    font-size: var(--font-size-body);
}

[data-ui-profiler-empty-panel] {
    min-height: 400px;
}

.tab-navigation .tab-control [data-ui-role="badge"] {
    margin-left: 8px;
}

#collector-content button.sf-toggle,
#collector-content a.sf-toggle {
    background: transparent;
    border: 0;
    box-shadow: none;
    color: var(--ui-color-primary);
    cursor: pointer;
    font: inherit;
    padding: 0;
    text-decoration: none;
}
#collector-content button.sf-toggle:hover,
#collector-content a.sf-toggle:hover {
    text-decoration: underline;
}
#collector-content .btn.btn-sm {
    font-size: 0.75rem;
    line-height: 1.25rem;
    padding: 0.1875rem 0.75rem;
}

div.table-with-search-field {
    position: relative;
}
div.table-with-search-field label.table-search-field-label {
    display: none;
}
div.table-with-search-field input.table-search-field-input {
    position: absolute;
    right: 5px;
    top: 5px;
    max-height: 27px; /* needed for Safari */
}
div.table-with-search-field .no-results-message {
    background: var(--ui-color-surface);
    border: solid var(--table-border-color);
    border-width: 0 1px 1px;
    border-bottom-left-radius: 6px;
    border-bottom-right-radius: 6px;
    font-size: var(--table-font-size);
    margin-top: -1em;
    padding: 15px 10px;
}

.block {
    display: block;
}
.full-width {
    width: 100%;
}
.hidden {
    display: none;
}
.nowrap {
    white-space: pre;
}
.prewrap {
    white-space: pre-wrap;
}
.newline {
    display: block;
}
.break-long-words {
    -ms-word-break: break-all;
    word-break: break-all;
    word-break: break-word;
    -webkit-hyphens: auto;
    -moz-hyphens: auto;
    hyphens: auto;
    hyphenate-character: '';
}
.text-small {
    font-size: 12px !important;
}
.text-muted {
    color: var(--ui-color-text-muted);
}
.text-danger {
    color: var(--ui-color-danger);
}
.text-bold {
    font-weight: bold;
}
.text-right {
    text-align: right;
}
.text-center {
    text-align: center;
}
.font-normal {
    font-family: var(--font-family-system);
    font-size: var(--font-size-body);
}
.help {
    color: var(--ui-color-text-muted);
    font-size: var(--font-size-body);
    margin: .5em 0;
}


/* Moved from deprecated panel.css — symfinity 044 wave 44.5b */

#collector-wrapper h2 {
    box-shadow: inset 0 -1px 0 var(--ui-color-surface), 0 1px 0 var(--h2-border-color), 0 4px 0 var(--ui-color-surface);
    padding-bottom: 5px;
}
#collector-content {
    margin: 0 0 15px 0;
    padding: 0;
}
@media (min-width: 768px) {
    #collector-content {
        margin: 0 0 30px 0;
        padding: 0;
    }
}
#main #collector-content > h2:first-of-type {
    box-shadow: inset 0 -1px 0 var(--ui-color-surface), 0 2px 0 var(--h2-border-color), 0 5px 0 var(--ui-color-surface);
    font-size: 24px;
    margin: 0 0 30px;
}
#main #collector-content > h2:first-of-type a {
    text-decoration: none;
}
#main #collector-content > h2:first-of-type a:hover {
    text-decoration: underline;
}

[data-ui-profiler-shell] #sidebar-shortcuts {
    padding: 0.3125rem 0.5rem;
}
[data-ui-profiler-shell] #sidebar-shortcuts .sf-tabs .tab-navigation {
    margin: 0.3125rem 0 0.9375rem;
}

#sidebar-search {
    padding: 5px 0;
}
#sidebar-search .form-group-row {
    display: flex;
    justify-content: space-between;
    padding-top: 10px;
}
#sidebar-search .form-group-row .form-group {
    padding-top: 0;
}
#sidebar-search .form-group {
    padding-top: 10px;
}
#sidebar-search .form-group:first-child {
    padding-top: 0;
}
#sidebar-search .form-group label {
    color: var(--ui-color-text-muted);
    display: block;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 3px;
}
#sidebar-search .form-group input,
#sidebar-search .form-group select {
    font-size: 13px;
}
#sidebar-search select#method {
    width: 11ch;
}
#sidebar-search input#status_code {
    width: 8ch;
}
#sidebar-search input#ip,
#sidebar-search input#url {
    width: calc(100% - 12px);
}
#sidebar-search .form-group-row-search-button {
    align-items: flex-end;
}


#menu-profiler {
    border-top: 1px solid var(--menu-border-color);
    list-style-type: none;
    margin: 0;
    padding: 7px;
    display: flex;
    flex-direction: column;
}
#menu-profiler li {
    margin-bottom: 0;
    position: relative;
}
#menu-profiler li:has(span.disabled) {
    order: 1;
}
#menu-profiler li + li {
    margin-top: 4px;
}
#menu-profiler li a:hover {
    text-decoration: none;
}

#menu-profiler li.selected::before {
    background: var(--menu-active-marker-background);
    border-radius: 6px;
    content: '';
    position: absolute;
    top: calc(50% - 14px);
    left: -8px;
    width: 4px;
    height: 28px;
}
#menu-profiler li.selected a [data-ui-profiler-menu-label],
#menu-profiler li a:hover [data-ui-profiler-menu-label] {
    background: var(--menu-active-background);
    color: var(--menu-active-color);
}
#menu-profiler li.selected a [data-ui-profiler-menu-icon] svg,
#menu-profiler li a:hover [data-ui-profiler-menu-icon] svg,
#menu-profiler li.selected a [data-ui-profiler-menu-disabled] [data-ui-profiler-menu-icon] svg,
#menu-profiler li a:hover [data-ui-profiler-menu-disabled] [data-ui-profiler-menu-icon] svg {
    color: var(--menu-active-color);
}

[data-ui-profiler-menu-icon] img,
[data-ui-profiler-menu-icon] svg {
    height: 24px;
    max-width: 24px;
}

[data-ui-profiler-menu-label] {
    align-items: center;
    background: transparent;
    border-radius: 4px;
    color: var(--menu-color);
    display: flex;
    gap: 0.5rem;
    overflow: hidden;
    padding: 5px 7px;
}
[data-ui-profiler-menu-icon] {
    color: var(--menu-icon-color);
    display: flex;
    margin-right: 0.25rem;
    text-align: center;
}
[data-ui-profiler-menu-label] strong {
    flex: 1;
    font-size: var(--font-size-body);
    font-weight: 500;
}
[data-ui-profiler-menu-label][data-ui-profiler-menu-disabled] {
    color: var(--menu-disabled-color);
    font-weight: normal;
}
[data-ui-profiler-menu-label][data-ui-profiler-menu-disabled] [data-ui-profiler-menu-icon] {
    color: var(--menu-icon-disabled-color);
}
[data-ui-profiler-menu-label][data-ui-profiler-menu-disabled] strong {
    font-weight: normal;
}

#collector-content [data-ui-profiler-metrics] {
    align-items: flex-start;
    display: flex;
    flex-wrap: wrap;
    gap: 1em;
    grid-template-columns: unset;
    margin: 1em 0;
}
#collector-content [data-ui-profiler-metrics] > .ui-profiler-metric-cell,
#collector-content [data-ui-profiler-metrics] > .ui-profiler-metric-group {
    flex: 0 0 auto;
}
.ui-profiler-metric-group {
    align-items: stretch;
    background: var(--ui-color-surface);
    border-radius: var(--ui-radius-md, 6px);
    box-shadow: inset 0 0 0 1px var(--ui-color-border), 0 0 0 5px var(--ui-color-surface);
    display: inline-flex;
    flex-direction: row;
    padding: 0.625rem 0;
}
.ui-profiler-metric-group > .ui-profiler-metric-cell {
    background: transparent;
    border-radius: 0;
    box-shadow: none;
    min-height: auto;
    min-width: 0;
}
.ui-profiler-metric-group > .ui-profiler-metric-cell + .ui-profiler-metric-cell {
    border-left: 1px solid var(--ui-color-border);
}
.ui-profiler-metric-cell {
    background: var(--ui-color-surface);
    border-radius: var(--ui-radius-md, 6px);
    box-shadow: inset 0 0 0 1px var(--ui-color-border), 0 0 0 5px var(--ui-color-surface);
    min-width: 5rem;
    padding: 0.625rem 0.9375rem;
    text-align: center;
}
.ui-profiler-metric__value {
    display: block;
    font-size: 1.5rem;
    font-variant-numeric: tabular-nums;
}
.ui-profiler-metric__unit {
    color: var(--ui-color-text-muted);
    font-size: 1.125rem;
    margin-left: -0.25rem;
}
.ui-profiler-metric__label {
    color: var(--ui-color-primary);
    display: block;
    font-size: 0.75rem;
}
.ui-profiler-metric__value svg {
    height: 2rem;
    width: 2rem;
}
.ui-profiler-metric__icon svg {
    height: 2rem;
    width: 2rem;
}
.ui-profiler-metric__icon.is-inactive svg {
    filter: grayscale(1);
    opacity: 0.4;
}
.theme-dark .ui-profiler-metric__icon.is-inactive svg {
    filter: invert(1) grayscale(1);
}

/* Performance panel (time collector) — ports stock time.css.twig (044 T028 gap) */
[data-ui-profiler-panel="time"] #timeline-control {
    align-items: center;
    background: var(--ui-color-surface);
    border: 1px solid var(--ui-color-border);
    border-radius: var(--ui-radius-md, 6px);
    display: flex;
    flex-wrap: wrap;
    gap: 0.5em 1em;
    margin: 1em 0;
    padding: 0.625rem 0.75rem;
}
[data-ui-profiler-panel="time"] #timeline-control label {
    font-weight: 600;
}
[data-ui-profiler-panel="time"] #timeline-control input[data-ui-role="input"] {
    max-width: 6em;
    text-align: right;
}
[data-ui-profiler-panel="time"] #timeline-control .help {
    color: var(--ui-color-text-muted);
    font-size: var(--ui-font-size-sm);
    margin: 0;
}

[data-ui-profiler-timeline] .legends {
    font-size: var(--ui-font-size-sm);
    line-height: 1.5;
    margin-bottom: 0.5em;
}
[data-ui-profiler-timeline] .legends .timeline-category {
    background: var(--ui-color-surface-elevated);
    border: 1px solid var(--ui-color-border);
    border-left-style: solid;
    border-left-width: 0.75em;
    border-radius: var(--ui-radius-sm, 4px);
    color: var(--ui-color-text);
    cursor: pointer;
    display: none;
    font: inherit;
    line-height: 1.4;
    margin: 0 0.5em 0.5em 0;
    opacity: 0.45;
    padding: 0.25em 0.625em 0.25em 0.5em;
}
[data-ui-profiler-timeline] .legends .timeline-category.active {
    opacity: 1;
}
[data-ui-profiler-timeline] .legends .timeline-category.present {
    display: inline-flex;
}
[data-ui-profiler-timeline] + p.help,
[data-ui-profiler-panel="time"] p.help {
    color: var(--ui-color-text-muted);
    font-size: var(--ui-font-size-sm);
    margin-top: 0;
}

[data-ui-profiler-timeline] .timeline-graph {
    background-color: var(--ui-color-surface);
    border: 1px solid var(--ui-color-border);
    border-radius: var(--ui-radius-sm, 4px);
    display: block;
    margin: 0 0 1em;
    min-height: 4rem;
    width: 100%;
}

[data-ui-profiler-timeline] .timeline-graph .timeline-label {
    fill: var(--ui-color-text);
    font-family: var(--ui-font-family-sans, system-ui, sans-serif);
    font-size: 12px;
    font-weight: normal;
    line-height: 12px;
}
[data-ui-profiler-timeline] .timeline-graph .timeline-label .timeline-sublabel {
    fill: var(--ui-color-text-muted);
}
[data-ui-profiler-timeline] .timeline-graph .timeline-subrequest,
[data-ui-profiler-timeline] .timeline-graph .timeline-border {
    fill: none;
    stroke: var(--ui-color-border);
    stroke-width: 1px;
}
[data-ui-profiler-timeline] .timeline-graph .timeline-subrequest {
    fill: url(#subrequest);
    fill-opacity: 0.5;
}
[data-ui-profiler-panel="time"] .timeline-subrequest-pattern {
    fill: color-mix(in srgb, var(--ui-color-text-muted) 35%, var(--ui-color-surface));
}
.theme-dark [data-ui-profiler-panel="time"] .timeline-subrequest-pattern {
    fill: color-mix(in srgb, var(--ui-color-text-muted) 55%, var(--ui-color-surface));
}
[data-ui-profiler-timeline] .timeline-graph .timeline-period {
    stroke-width: 0;
}

/* Logger + Events panel filters — shared chip summary (044 gap) */
[data-ui-profiler-panel="logger"],
[data-ui-profiler-panel="events"] {
    --log-filter-active-num-color: var(--ui-color-primary);
}

[data-ui-profiler-panel="logger"] {
    --log-timestamp-color: var(--ui-color-text-muted);
}

[data-ui-profiler-panel="logger"] .log-filters,
[data-ui-profiler-panel="events"] .event-filters {
    align-items: flex-start;
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin: 0 0 1em;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter,
[data-ui-profiler-panel="events"] .event-filters .log-filter {
    flex-shrink: 0;
    position: relative;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter > summary,
[data-ui-profiler-panel="events"] .event-filters .log-filter > summary {
    align-items: center;
    background: var(--ui-color-surface-elevated);
    border: 1px solid var(--ui-color-border);
    border-radius: var(--ui-radius-sm, 4px);
    box-shadow: 0 1px 2px color-mix(in srgb, var(--ui-color-text) 6%, transparent);
    color: var(--ui-color-text);
    cursor: pointer;
    display: inline-flex;
    font-size: var(--ui-font-size-sm);
    font-weight: 600;
    gap: 0.35rem;
    list-style: none;
    padding: 0.35rem 0.625rem;
    white-space: nowrap;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter > summary::-webkit-details-marker,
[data-ui-profiler-panel="events"] .event-filters .log-filter > summary::-webkit-details-marker {
    display: none;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter > summary .icon,
[data-ui-profiler-panel="events"] .event-filters .log-filter > summary .icon {
    display: inline-flex;
    height: 1.125rem;
    width: 1.125rem;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter > summary svg,
[data-ui-profiler-panel="events"] .event-filters .log-filter > summary svg {
    height: 1.125rem;
    opacity: 0.75;
    stroke-width: 2;
    width: 1.125rem;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter .log-filter-summary,
[data-ui-profiler-panel="events"] .event-filters .log-filter .log-filter-summary {
    align-items: center;
    display: inline-flex;
    flex-wrap: wrap;
    gap: 0.35rem;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter .filter-name,
[data-ui-profiler-panel="events"] .event-filters .log-filter .filter-name {
    font-weight: 600;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter .filter-active-value,
[data-ui-profiler-panel="events"] .event-filters .log-filter .filter-active-value {
    color: var(--log-filter-active-num-color);
    font-weight: 700;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter .filter-active-badge,
[data-ui-profiler-panel="events"] .event-filters .log-filter .filter-active-badge {
    background: color-mix(in srgb, var(--ui-color-text-muted) 14%, var(--ui-color-surface));
    border-radius: var(--ui-radius-sm, 4px);
    color: var(--ui-color-text);
    display: inline-block;
    font-size: var(--ui-font-size-sm);
    font-weight: 600;
    line-height: 1.2;
    padding: 0.125rem 0.375rem;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter .log-filter-content,
[data-ui-profiler-panel="events"] .event-filters .log-filter .log-filter-content {
    background: var(--ui-color-surface);
    border: 1px solid var(--ui-color-border);
    border-radius: var(--ui-radius-md, 6px);
    box-shadow: 0 10px 15px -3px color-mix(in srgb, var(--ui-color-text) 12%, transparent),
        0 4px 6px -2px color-mix(in srgb, var(--ui-color-text) 8%, transparent);
    left: 0;
    max-width: 24rem;
    min-width: 12.5rem;
    padding: 0.9375rem;
    position: absolute;
    top: calc(100% + 0.35rem);
    z-index: 9999;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter .log-filter-option,
[data-ui-profiler-panel="events"] .event-filters .log-filter .log-filter-option {
    align-items: center;
    display: flex;
    gap: 0.5rem;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter .log-filter-option + .log-filter-option,
[data-ui-profiler-panel="events"] .event-filters .log-filter .log-filter-option + .log-filter-option {
    margin-top: 0.45rem;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter .log-filter-option label,
[data-ui-profiler-panel="events"] .event-filters .log-filter .log-filter-option label {
    align-items: center;
    cursor: pointer;
    display: inline-flex;
    flex: 1;
    gap: 0.5rem;
}
[data-ui-profiler-panel="logger"] .log-filters .log-filter .log-filter-option label .badge,
[data-ui-profiler-panel="events"] .event-filters .log-filter .log-filter-option label .badge {
    background: color-mix(in srgb, var(--ui-color-text-muted) 14%, var(--ui-color-surface));
    border-radius: var(--ui-radius-sm, 4px);
    color: var(--ui-color-text);
    font-size: var(--ui-font-size-sm);
    font-weight: 600;
    line-height: 1.2;
    margin-left: auto;
    padding: 0.125rem 0.375rem;
}
[data-ui-profiler-panel="logger"] .log-filter .filter-select-all-or-none {
    margin-bottom: 0.625rem;
}
[data-ui-profiler-panel="logger"] .log-filter .filter-select-all-or-none button + button {
    margin-left: 0.9375rem;
}
[data-ui-profiler-panel="logger"] .log-filter .btn.btn-link {
    background: transparent;
    border: 0;
    box-shadow: none;
    color: var(--ui-color-primary);
    cursor: pointer;
    font: inherit;
    font-size: var(--ui-font-size-sm);
    padding: 0;
    text-decoration: none;
}
[data-ui-profiler-panel="logger"] .log-filter .btn.btn-link:hover {
    text-decoration: underline;
}

[data-ui-profiler-panel="logger"] table.logs {
    border-bottom-width: 0;
    border-collapse: collapse;
}
[data-ui-profiler-panel="logger"] table.logs tr + tr td {
    border-width: 1px 0 0;
}
[data-ui-profiler-panel="logger"] table.logs .log-timestamp {
    color: var(--log-timestamp-color);
    white-space: nowrap;
}
[data-ui-profiler-panel="logger"] table.logs .log-metadata {
    margin: 0.5rem 0 0;
}
[data-ui-profiler-panel="logger"] table.logs .log-metadata > span {
    display: inline-block;
}
[data-ui-profiler-panel="logger"] table.logs .log-metadata > span + span {
    margin-left: 0.625rem;
}
[data-ui-profiler-panel="logger"] table.logs .log-metadata .log-num-occurrences {
    color: var(--ui-color-text-muted);
    font-size: var(--ui-font-size-sm);
}
[data-ui-profiler-panel="logger"] table.logs .log-metadata .context {
    background: var(--ui-color-surface-elevated);
    border-radius: var(--ui-radius-sm, 4px);
    padding: 0.3125rem;
}
[data-ui-profiler-panel="logger"] table.logs .log-metadata .context + .context {
    margin-top: 0.625rem;
}

[data-ui-profiler-panel="logger"] .badge {
    border-radius: var(--ui-radius-sm, 4px);
    display: inline-block;
    font-size: var(--ui-font-size-sm);
    font-weight: 600;
    line-height: 1.2;
    padding: 0.125rem 0.375rem;
}
[data-ui-profiler-panel="logger"] .badge.status-error,
[data-ui-profiler-panel="logger"] .log-type-badge.badge-error {
    background: color-mix(in srgb, var(--ui-color-danger) 18%, var(--ui-color-surface));
    color: var(--ui-color-danger);
}
[data-ui-profiler-panel="logger"] .badge.status-warning,
[data-ui-profiler-panel="logger"] .log-type-badge.badge-warning,
[data-ui-profiler-panel="logger"] .log-type-badge.badge-deprecation {
    background: color-mix(in srgb, var(--ui-color-warning) 18%, var(--ui-color-surface));
    color: var(--ui-color-warning);
}
[data-ui-profiler-panel="logger"] .log-type-badge {
    background: color-mix(in srgb, var(--ui-color-text-muted) 14%, var(--ui-color-surface));
    box-shadow: none;
    color: var(--ui-color-text);
    display: inline-block;
    font-family: var(--font-family-system);
    margin-top: 0.3125rem;
}
[data-ui-profiler-panel="logger"] .log-type-badge.badge-silenced {
    background: color-mix(in srgb, #6d28d9 18%, var(--ui-color-surface));
    color: #6d28d9;
}
.theme-dark [data-ui-profiler-panel="logger"] .log-type-badge.badge-silenced {
    background: color-mix(in srgb, #6d28d9 35%, var(--ui-color-surface));
    color: #ede9fe;
}

[data-ui-profiler-panel="logger"] tr.log-status-warning > td:first-child,
[data-ui-profiler-panel="logger"] tr.log-status-error > td:first-child,
[data-ui-profiler-panel="logger"] tr.log-status-silenced > td:first-child {
    position: relative;
}
[data-ui-profiler-panel="logger"] tr.log-status-warning > td:first-child::before,
[data-ui-profiler-panel="logger"] tr.log-status-error > td:first-child::before,
[data-ui-profiler-panel="logger"] tr.log-status-silenced > td:first-child::before {
    background: transparent;
    border-radius: 0;
    content: '';
    height: 100%;
    left: 0;
    position: absolute;
    top: 0;
    width: 4px;
}
[data-ui-profiler-panel="logger"] tr.log-status-warning > td:first-child::before {
    background: var(--ui-color-warning);
}
[data-ui-profiler-panel="logger"] tr.log-status-error > td:first-child::before {
    background: var(--ui-color-danger);
}
[data-ui-profiler-panel="logger"] tr.log-status-silenced > td:first-child::before {
    background: #a78bfa;
}

[data-ui-profiler-panel="logger"] .container-compilation-logs {
    background: var(--ui-color-surface);
    border: 1px solid var(--ui-color-border);
    border-radius: var(--ui-radius-md, 6px);
    margin-top: 1.875rem;
    padding: 0.9375rem;
}
[data-ui-profiler-panel="logger"] .container-compilation-logs summary {
    cursor: pointer;
}
[data-ui-profiler-panel="logger"] .container-compilation-logs summary h4 {
    margin: 0 0 0.3125rem;
}
[data-ui-profiler-panel="logger"] .container-compilation-logs summary p {
    color: var(--ui-color-text-muted);
    margin: 0;
}

/* Events panel — dispatcher/listener content */
[data-ui-profiler-panel="events"] table[data-ui-role="table"] tbody th.colored {
    background: var(--ui-color-surface-elevated);
    color: var(--ui-color-primary);
    font-weight: 600;
    text-align: left;
}
[data-ui-profiler-panel="events"] .events-listener-name {
    background: transparent;
    color: inherit;
    font-family: var(--font-family-monospace);
    font-size: var(--font-size-monospace);
    padding: 0;
}

#header,
[data-ui-profiler-header] {
    flex: 0 0 auto;
    align-items: center;
    display: flex;
    justify-content: space-between;
    overflow: hidden;
    padding: var(--ui-space-2, 0.5rem) 0;
}
#header h1,
[data-ui-profiler-header] h1 {
    align-items: center;
    display: flex;
    font-size: 1.125rem;
    gap: 0.375rem;
    margin: 0;
}
#header h1 svg,
[data-ui-profiler-header] h1 svg {
    height: 1.75rem;
    width: 1.75rem;
}
#header .search input,
[data-ui-profiler-header] .search input {
    max-width: 14rem;
}

#summary {
    background: var(--page-background);
    box-shadow: 0 0 0 5px var(--page-background);
    color: var(--ui-color-text);
    margin: 0 0 15px;
}

#summary .status {
    background: var(--header-background);
    border-radius: 6px;
    color: var(--header-metadata-value);
    padding: calc(15px + var(--summary-status-border-width)) 15px 15px;
    position: relative;
}
#summary .status:before {
    background: var(--header-border-color);
    border-radius: 3px 3px 0 0;
    content: '';
    height: var(--summary-status-border-width);
    left: 0;
    position: absolute;
    top: 0;
    width: 100%;
}
#summary .status-compact {
    font-size: 13px;
    margin: 0 0 10px;
    padding: 13px 15px 10px;
    position: relative;
}
#summary .status-compact.status-compact-forward {
    padding: 10px 15px;
}
#summary .status + .status {
    margin-top: 10px;
}
#summary h2 {
    align-items: flex-start;
    display: flex;
}
#summary h2,
#summary h2 a {
    display: flex;
    font-size: 24px;
    line-height: 1.25;
    margin: 0;
    text-decoration: none;
    word-break: break-all;
}
#summary h2 a:hover {
    text-decoration: underline;
}
#summary .status .metadata .icon-referer {
    color: var(--color-link);
    display: inline-block;
    height: 21px;
    vertical-align: middle;
    width: 21px;
}
#summary .status .metadata .icon-referer svg {
    height: 21px;
    width: 21px;
}
#summary .status .metadata a.referer {
    color: var(--color-link);
}
#summary .status .metadata a.referer:hover {
    text-decoration: underline;
}
#summary .status-compact .status-request-method {
    border-width: 1px;
    font-size: 12px;
    font-weight: bold;
    margin: 0 2px;
    padding: 1px 2px;
    white-space: nowrap;
}
#summary .status-compact .icon {
    display: inline-block;
    transform: translateY(2px);
    vertical-align: bottom;
}
#summary .status-compact .icon,
#summary .status-compact .icon svg {
    height: 21px;
    width: 21px;
}
#summary .status-compact .icon svg {
    color: var(--ui-color-text-muted);
}
.theme-dark #summary .status-compact .icon svg {
    color: var(--ui-color-border);
}
#summary .status-compact .icon.icon-redirect svg,
#summary .status-compact .icon.icon-forward svg {
    stroke-width: 2;
}
#summary .status-compact .icon.icon-redirect svg {
    transform: rotate(90deg) translateX(3px);
}
#summary .status-compact.status-warning .icon svg {
    color: var(--ui-color-warning);
}
#summary .status-response-status-code {
    background: var(--ui-color-text-muted);
    border-radius: 4px;
    color: var(--ui-color-surface);
    display: inline-block;
    font-size: 12px;
    font-weight: bold;
    margin-right: 2px;
    padding: 1px 3px;
}
#summary .status-response-status-text {
    font-weight: bold;
}
#summary .status-success {
    background: var(--header-success-background);
    box-shadow: var(--header-success-box-shadow);
}
#summary .status-success:before {
    background: var(--header-success-border-color);
}
#summary .status-success h2 a {
    color: var(--header-success-title-color);
}
#summary .status-success .status-response-status-code {
    background: var(--header-success-status-code-background);
    color: var(--header-success-status-code-color);
}
#summary .status-success .status-response-status-text {
    color: var(--header-success-status-text-color);
}
#summary .status-warning {
    background: var(--header-warning-background);
    box-shadow: var(--header-warning-box-shadow);
}
#summary .status-warning:before {
    background: var(--header-warning-border-color);
}
#summary .status-warning h2 a {
    color: var(--header-warning-title-color);
}
#summary .status-warning .status-response-status-code,
#summary .summary-status-redirect .status-response-status-code {
    background: var(--header-warning-status-code-background);
    color: var(--header-warning-status-code-color);
}
#summary .status-warning .status-response-status-text {
    color: var(--header-warning-status-text-color);
}
#summary .status-error {
    background: var(--header-error-background);
    box-shadow: var(--header-error-box-shadow);
}
#summary .status-error:before {
    background: var(--header-error-border-color);
}
#summary .status-error h2 a {
    color: var(--header-error-title-color);
}
#summary .status-error .status-response-status-code {
    background: var(--header-error-status-code-background);
    color: var(--header-error-status-code-color);
}
#summary .status-error .status-response-status-text {
    color: var(--header-error-status-text-color);
}
#summary .status-request-method {
    border: 1px solid var(--header-status-request-method-color);
    border-radius: 5px;
    color: var(--header-status-request-method-color);
    display: inline-block;
    font-size: 16px;
    line-height: 1;
    margin-right: 6px;
    padding: 2px 4px;
    text-align: center;
    white-space: nowrap;
}
#summary .status:not(.status-compact) .status-request-method {
    transform: translateY(5px);
}
#summary .status-error-details {
    align-items: center;
    display: flex;
    font-size: 13px;
    line-height: 1;
    margin: 0 0 10px;
}
#summary .status-error-details .icon {
    display: inline-block;
}
#summary .status-error-details .icon svg {
    border-radius: 50%;
    box-shadow: inset 0 0 0 2px color-mix(in srgb, var(--ui-color-danger) 35%, transparent);
    color: var(--ui-color-danger);
    fill: color-mix(in srgb, var(--ui-color-danger) 12%, var(--ui-color-surface));
    height: 24px;
    stroke-width: 3px;
    width: 24px;
}
#summary .status-error-details .status-response-status-code {
    font-size: 15px;
    font-weight: bold;
    letter-spacing: -0.5px;
    line-height: 1;
    padding: 5px 8px;
    text-transform: uppercase;
}
#summary .status-error-details .status-response-status-text {
    color: var(--header-error-status-text-color);
    font-weight: normal;
}
#summary dl.metadata {
    margin: 10px 0 0;
}
#summary dl.metadata dt,
#summary dl.metadata dd {
    display: inline-block;
    font-size: 13px;
}
#summary dl.metadata dt {
    color: var(--header-metadata-key);
    font-weight: bold;
}
#summary dl.metadata dt:not(:empty):after {
    content: ':';
}
#summary dl.metadata dd,
#summary dl.metadata dd a {
    color: var(--header-metadata-value);
}
#summary dl.metadata dd {
    margin: 0 1.5em 0 0;
}
.tab-navigation {
    background-color: var(--tab-background);
    border-radius: 6px;
    box-shadow: inset 0 0 0 1px var(--tab-border-color), 0 0 0 5px var(--ui-color-surface);
    display: inline-flex;
    flex-wrap: wrap;
    margin: 0 0 15px;
    padding: 0;
    user-select: none;
    -webkit-user-select: none;
}
.sf-tabs-sm .tab-navigation {
    box-shadow: inset 0 0 0 1px var(--tab-border-color), 0 0 0 4px var(--ui-color-surface);
    margin: 0 0 10px;
}
.tab-navigation .tab-control {
    background: transparent;
    color: inherit;
    border: 0;
    box-shadow: none;
    transition: box-shadow .05s ease-in, background-color .05s ease-in;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    line-height: 1.4;
    margin: 0;
    padding: 4px 14px;
    position: relative;
    text-align: center;
    z-index: 1;
}
.tab-navigation .tab-control a {
    color: var(--ui-color-text);
    text-decoration: none;
}
.tab-navigation .tab-control.active a {
    color: var(--tab-active-color);
}
.sf-tabs-sm .tab-navigation .tab-control {
    font-size: 13px;
    padding: 2.5px 10px;
}
.tab-navigation .tab-control:before {
    background: var(--tab-border-color);
    bottom: 15%;
    content: "";
    left: 0;
    position: absolute;
    top: 15%;
    width: 1px;
}
.tab-navigation .tab-control:first-child:before,
.tab-navigation .tab-control.active + .tab-control:before,
.tab-navigation .tab-control.active:before {
    width: 0;
}
.tab-navigation .tab-control .badge {
    background: color-mix(in srgb, var(--ui-color-primary) 14%, var(--ui-color-surface));
    border-radius: var(--ui-radius-sm, 4px);
    color: var(--ui-color-primary);
    display: inline-block;
    font-size: var(--ui-font-size-sm);
    font-weight: 700;
    line-height: 1;
    margin-left: 0.5rem;
    min-width: 1.25rem;
    padding: 0.125rem 0.375rem;
    text-align: center;
    white-space: nowrap;
}
.tab-navigation .tab-control.disabled {
    color: var(--tab-disabled-color);
}
.tab-navigation .tab-control.active {
    background-color: var(--tab-active-background);
    border-radius: 6px;
    box-shadow: inset 0 0 0 1.5px var(--tab-active-border-color);
    color: var(--tab-active-color);
    position: relative;
    z-index: 1;
}
.tab-content > *:first-child {
    margin-top: 0;
}


.sf-tabs .tab:not(:first-child) { display: none; }

.sf-toggle-content.sf-toggle-hidden {
    display: none;
}
.sf-toggle-content.sf-toggle-visible {
    display: block;
}

pre.sf-dump, pre.sf-dump .sf-dump-default {
    white-space: pre-wrap;
    z-index: 1000 !important;
}

#collector-content .sf-dump {
    margin: 0 0 2em;
    padding: 0;
    line-height: 1.4;
}
#collector-content [data-ui-role="table"] td .sf-dump,
#collector-content [data-ui-role="table"] th .sf-dump,
#collector-content [data-ui-role="table"] td pre.sf-dump,
#collector-content [data-ui-role="table"] th pre.sf-dump,
#collector-content [data-ui-role="table"] td .sf-dump pre.sf-dump,
#collector-content [data-ui-role="table"] th .sf-dump pre.sf-dump {
    margin-bottom: 0;
}
#collector-content pre.sf-dump,
#collector-content .sf-dump code,
#collector-content .sf-dump samp {
    font-family: var(--font-family-monospace);
    font-size: var(--font-size-monospace);
    font-variant-ligatures: var(--font-variant-ligatures-monospace);
    font-weight: normal;
}
#collector-content pre.sf-dump,
#collector-content .sf-dump-default {
    background: none;
    color: var(--ui-color-text);
}
#collector-content .sf-dump a { cursor: pointer; }
#collector-content .sf-dump pre.sf-dump,
#collector-content .sf-dump .trace {
    border: var(--border);
    padding: 10px;
    margin: 0.5em 0;
    overflow: auto;
    background: var(--ui-color-surface);
    font-size: 12px;
}
#collector-content .sf-dump .trace li { margin-bottom: 0; padding: 0; }
#collector-content .sf-dump-ellipsis { max-width: 100em; color: var(--ui-color-text-muted); }
#collector-content .sf-dump-ellipsis-path { max-width: 5em; }
#collector-content .dump-inline .sf-dump {
    display: inline;
    white-space: normal;
    line-height: inherit;
}
#collector-content .dump-inline .sf-dump:after { display: none; }
#collector-content .sf-dump samp { line-height: 1.7; }
#collector-content .sf-dump-expanded,
#collector-content .sf-dump-private,
#collector-content .sf-dump-protected,
#collector-content .sf-dump-public { color: var(--ui-color-text); }
#collector-content .sf-dump-str { color: var(--highlight-string); }
#collector-content .sf-dump-note { color: var(--ui-color-warning); }
#collector-content .sf-dump-meta { color: var(--ui-color-primary); }
#collector-content .sf-dump-key { color: var(--highlight-string); }
#collector-content .sf-dump-num,
#collector-content .sf-dump-const { color: var(--highlight-constant); }
#collector-content .sf-dump-ref { color: var(--ui-color-text-muted); }
#collector-content .sf-dump .trace li.selected { background: var(--highlight-selected-line); }

.sql-runnable {
    background: var(--base-1);
    margin: .5em 0;
    padding: 1em;
}
.sql-explain {
    overflow-x: auto;
    max-width: 888px;
}
body.width-full .sql-explain,
[data-ui-profiler-shell].width-full .sql-explain {
    max-width: unset;
}
.sql-explain table td, .sql-explain table tr {
    word-break: normal;
}
.queries-table pre {
    margin: 0;
    white-space: pre-wrap;
    -ms-word-break: break-all;
    word-break: break-all;
    word-break: break-word;
    -webkit-hyphens: auto;
    -moz-hyphens: auto;
    hyphens: auto;
}

.visible-small {
    display: none;
}
.hidden-small {
    display: inherit;
}


/* === CO-* panel scoped rules (044 SC-007 — merged from assets/panels/) === */

#collector-content [data-ui-profiler-panel] .trace {
    border: var(--border);
    background: var(--ui-color-surface);
    padding: 10px;
    margin: 0.5em 0;
    overflow: auto;
    font-size: 12px;
}
#collector-content [data-ui-profiler-panel] .trace li { margin-bottom: 0; padding: 0; }
#collector-content [data-ui-profiler-panel] .trace li.selected { background: var(--highlight-selected-line); }
.sf-serializer, .sf-validator { margin-bottom: 2em; }
[data-ui-profiler-panel="request"] .ui-profiler-columns-3 {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: var(--ui-grid-gap);
    align-items: stretch;
}
[data-ui-profiler-panel="request"] .ui-profiler-columns-3 h3 { margin-top: 0; }
[data-ui-profiler-panel="request"] .ui-profiler-columns-3 [data-ui-role="empty"] { margin-bottom: 0; }

/* Profiler shell two-column layout — owned here; theme token schema is 1.0-only and ui-kernel does not emit [data-ui-role="grid"] rules at 1.0. */
[data-ui-profiler-shell] #main[data-ui-role="grid"] {
    display: grid;
    gap: var(--ui-grid-gap, var(--ui-space-md, 1rem));
    grid-template-columns: 1fr;
}

[data-ui-profiler-shell] #main[data-ui-role="grid"] > [data-ui-role="grid-cell"] {
    grid-column: 1 / -1;
    min-width: 0;
}

@media (min-width: 768px) {
    [data-ui-profiler-shell] #main[data-ui-role="grid"] {
        grid-template-columns: repeat(12, minmax(0, 1fr));
    }

    [data-ui-profiler-shell] #main[data-ui-role="grid"] > [data-ui-role="grid-cell"][data-ui-span-md="3"],
    [data-ui-profiler-shell] #main[data-ui-role="grid"] > [data-ui-role="grid-cell"][data-ui-span-lg="3"] {
        grid-column: span 3;
    }

    [data-ui-profiler-shell] #main[data-ui-role="grid"] > [data-ui-role="grid-cell"][data-ui-span-md="9"],
    [data-ui-profiler-shell] #main[data-ui-role="grid"] > [data-ui-role="grid-cell"][data-ui-span-lg="9"] {
        grid-column: span 9;
    }
}
CSS;
}
