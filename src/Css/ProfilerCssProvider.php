<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Css;

use Symfinity\UiProfiler\Asset\ShellChromeStylesheet;
use Symfinity\UiProfiler\Asset\WdtCriticalCss;
use Symfinity\UiProfiler\Asset\WdtToolbarStylesheet;

/**
 * Reads committed profiler CSS from assets/styles/ (author SCSS in assets/scss/).
 */
final class ProfilerCssProvider
{
    public function __construct(
        private readonly string $packageDir,
    ) {
    }

    public function shellStylesheet(): string
    {
        return self::readFile($this->packageDir, ShellChromeStylesheet::STYLES_PATH);
    }

    public function wdtToolbarStylesheet(): string
    {
        return self::readFile($this->packageDir, WdtToolbarStylesheet::STYLES_PATH);
    }

    public function wdtCriticalCss(): string
    {
        return WdtCriticalCss::css($this->packageDir);
    }

    public static function readFile(string $packageDir, string $relativePath): string
    {
        $path = $packageDir.'/'.$relativePath;
        if (!is_readable($path)) {
            throw new \RuntimeException(sprintf('Profiler stylesheet missing: %s', $relativePath));
        }

        return (string) file_get_contents($path);
    }
}
