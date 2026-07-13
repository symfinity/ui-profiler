<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('ui-profiler-parity')]
final class CssBudgetTest extends TestCase
{
    private const BASELINE_PATH = __DIR__.'/../baselines/css-budget-baseline.json';

    public function testCompiledCssInventoryDoesNotExceedBaseline(): void
    {
        /** @var array{files: array<string, int>} $baseline */
        $baseline = json_decode((string) file_get_contents(self::BASELINE_PATH), true, 512, \JSON_THROW_ON_ERROR);
        $packageDir = \dirname(__DIR__, 2);
        $violations = [];

        foreach ($baseline['files'] as $relativePath => $maxLines) {
            $absolutePath = $packageDir.'/'.$relativePath;
            self::assertFileExists($absolutePath, sprintf('CSS budget file missing: %s', $relativePath));

            $lineCount = self::countNonEmptyLines($absolutePath);
            if ($lineCount > $maxLines) {
                $violations[] = sprintf(
                    '%s: %d lines (baseline max %d) — shrink or lower baseline in css-budget-baseline.json',
                    $relativePath,
                    $lineCount,
                    $maxLines,
                );
            }
        }

        self::assertSame([], $violations);
    }

    public function testExitPolicyIsSassAuthorPipeline(): void
    {
        /** @var array{exit_policy?: string} $baseline */
        $baseline = json_decode((string) file_get_contents(self::BASELINE_PATH), true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame('sass_author_pipeline', $baseline['exit_policy'] ?? null);
    }

    public function testOnlyAllowlistedCssExistsUnderAssets(): void
    {
        /** @var array{files: array<string, int>} $baseline */
        $baseline = json_decode((string) file_get_contents(self::BASELINE_PATH), true, 512, \JSON_THROW_ON_ERROR);
        $packageDir = \dirname(__DIR__, 2);
        $allowed = array_keys($baseline['files']);
        $violations = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($packageDir.'/assets', \FilesystemIterator::SKIP_DOTS),
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || !str_ends_with($file->getPathname(), '.css')) {
                continue;
            }

            $relativePath = 'assets/'.str_replace('\\', '/', substr($file->getPathname(), strlen($packageDir.'/assets') + 1));
            if (!\in_array($relativePath, $allowed, true)) {
                $violations[] = sprintf(
                    '%s: unexpected owned CSS — add to css-budget-baseline.json or remove file',
                    $relativePath,
                );
            }
        }

        self::assertSame([], $violations);
    }

    public function testOwnedCssDoesNotDeclareSfPrefixedCustomProperties(): void
    {
        $stylesDir = \dirname(__DIR__, 2).'/assets/styles';
        $violations = [];

        foreach (glob($stylesDir.'/*.css') ?: [] as $path) {
            $contents = (string) file_get_contents($path);
            if (preg_match('/--sf-[a-z0-9-]+/i', $contents, $matches)) {
                $violations[] = 'assets/styles/'.basename($path).': '.$matches[0];
            }
        }

        self::assertSame([], $violations, 'Use --ui-toolbar-* or --ui-profiler-* tokens in owned CSS — not --sf-*.');
    }

    public function testAuthorScssIsFreshAgainstCompiledCss(): void
    {
        /** @var array{author_scss: list<string>} $baseline */
        $baseline = json_decode((string) file_get_contents(self::BASELINE_PATH), true, 512, \JSON_THROW_ON_ERROR);
        $packageDir = \dirname(__DIR__, 2);
        $violations = [];

        foreach ($baseline['author_scss'] as $scssRelative) {
            $scssPath = $packageDir.'/'.$scssRelative;
            if (!is_file($scssPath)) {
                self::markTestSkipped('Author SCSS not present — split mirror excludes assets/scss; freshness gate runs in monorepo only.');

                return;
            }
        }

        foreach ($baseline['author_scss'] as $scssRelative) {
            $scssPath = $packageDir.'/'.$scssRelative;
            $cssRelative = preg_replace('#^assets/scss/#', 'assets/styles/', $scssRelative);
            $cssRelative = preg_replace('#\.scss$#', '.css', (string) $cssRelative);
            $cssPath = $packageDir.'/'.$cssRelative;

            self::assertFileExists($scssPath, $scssRelative);
            self::assertFileExists($cssPath, $cssRelative.' — run bin/profiler-css-compile');

            if (filemtime($scssPath) > filemtime($cssPath)) {
                $violations[] = sprintf('%s is newer than %s', $scssRelative, $cssRelative);
            }
        }

        self::assertSame([], $violations, 'Stale compiled CSS — run packages/ui-profiler/bin/profiler-css-compile');
    }

    private static function countNonEmptyLines(string $path): int
    {
        $count = 0;

        foreach (file($path, \FILE_IGNORE_NEW_LINES) ?: [] as $line) {
            $trimmed = trim($line);
            if ('' === $trimmed || str_starts_with($trimmed, '/*') && str_ends_with($trimmed, '*/')) {
                continue;
            }
            ++$count;
        }

        return $count;
    }
}
