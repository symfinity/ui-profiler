<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Asset;

use Symfinity\UiProfiler\Css\ProfilerCssProvider;

/**
 * First-paint WDT skeleton — compiled CSS at {@see STYLES_PATH} (inline in toolbar_js when enabled).
 *
 * Minimal subset of {@see WdtToolbarStylesheet}; keep selectors aligned when changing toggle geometry.
 */
final class WdtCriticalCss
{
    public const STYLES_PATH = 'assets/styles/wdt-critical.css';

    public static function css(?string $packageDir = null): string
    {
        return ProfilerCssProvider::readFile($packageDir ?? \dirname(__DIR__, 2), self::STYLES_PATH);
    }

    private function __construct()
    {
    }
}
