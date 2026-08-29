<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Asset;

use Symfinity\UiProfiler\Css\ProfilerCssProvider;

/**
 * WDT toolbar rules for /_wdt/styles — compiled CSS at {@see STYLES_PATH}.
 *
 * {@see WdtCriticalCss} mirrors a subset for first-paint inlining; keep toggle-button
 * geometry in sync when editing assets/scss/wdt-toolbar.scss or wdt-critical.scss.
 */
final class WdtToolbarStylesheet
{
    public const STYLES_PATH = 'assets/styles/wdt-toolbar.css';

    public static function css(?string $packageDir = null): string
    {
        return ProfilerCssProvider::readFile($packageDir ?? \dirname(__DIR__, 2), self::STYLES_PATH);
    }

    private function __construct()
    {
    }
}
