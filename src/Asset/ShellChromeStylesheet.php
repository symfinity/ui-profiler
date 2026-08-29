<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Asset;

use Symfinity\UiProfiler\Css\ProfilerCssProvider;

/**
 * Profiler shell chrome — compiled CSS at {@see STYLES_PATH} (author: assets/scss/shell-chrome.scss).
 */
final class ShellChromeStylesheet
{
    public const STYLES_PATH = 'assets/styles/shell-chrome.css';

    public static function css(?string $packageDir = null): string
    {
        return ProfilerCssProvider::readFile($packageDir ?? \dirname(__DIR__, 2), self::STYLES_PATH);
    }

    private function __construct()
    {
    }
}
