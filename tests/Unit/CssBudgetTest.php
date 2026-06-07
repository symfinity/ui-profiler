<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('ui-profiler-parity')]
final class CssBudgetTest extends TestCase
{
    private const BASELINE_PATH = __DIR__.'/../Fixtures/css-budget-baseline.json';

    public function testDeprecatedCssInventoryDoesNotExceedBaseline(): void
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
                    '%s: %d lines (baseline max %d) — shrink or lower baseline in css-budget-baseline.json per css-strangler-migration.md',
                    $relativePath,
                    $lineCount,
                    $maxLines,
                );
            }
        }

        self::assertSame([], $violations);
    }

    public function testExitCapsDocumentedInBaselineFixture(): void
    {
        /** @var array{exit_caps: array<string, int>, exit_policy?: string} $baseline */
        $baseline = json_decode((string) file_get_contents(self::BASELINE_PATH), true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame('zero_owned_css', $baseline['exit_policy'] ?? null);

        foreach ($baseline['exit_caps'] as $relativePath => $exitCap) {
            self::assertSame(
                0,
                $exitCap,
                sprintf('Exit cap for %s must be 0 — SC-007 zero owned CSS', $relativePath),
            );
        }
    }

    public function testDeprecatedCssInventoryDoesNotExceedExitCaps(): void
    {
        /** @var array{exit_caps: array<string, int>} $baseline */
        $baseline = json_decode((string) file_get_contents(self::BASELINE_PATH), true, 512, \JSON_THROW_ON_ERROR);
        $packageDir = \dirname(__DIR__, 2);
        $violations = [];

        foreach ($baseline['exit_caps'] as $relativePath => $exitCap) {
            $absolutePath = $packageDir.'/'.$relativePath;
            if (!is_file($absolutePath)) {
                continue;
            }

            $lineCount = self::countNonEmptyLines($absolutePath);
            if ($lineCount > $exitCap) {
                $violations[] = sprintf(
                    '%s: %d lines (exit cap %d) — css-strangler-migration SC-007 not met',
                    $relativePath,
                    $lineCount,
                    $exitCap,
                );
            }
        }

        self::assertSame([], $violations);
    }

    public function testAllAssetsCssFilesMeetZeroExitCap(): void
    {
        $assetsDir = \dirname(__DIR__, 2).'/assets';
        $violations = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($assetsDir, \FilesystemIterator::SKIP_DOTS),
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || !str_ends_with($file->getPathname(), '.css')) {
                continue;
            }

            $relativePath = 'assets/'.str_replace('\\', '/', substr($file->getPathname(), strlen($assetsDir) + 1));
            $lineCount = self::countNonEmptyLines($file->getPathname());
            if ($lineCount > 0) {
                $violations[] = sprintf(
                    '%s: %d lines (exit cap 0) — delete file or move styling to UI Kernel + ux-blocks',
                    $relativePath,
                    $lineCount,
                );
            }
        }

        self::assertSame([], $violations);
    }

    public function testOwnedCssDoesNotDeclareSfPrefixedCustomProperties(): void
    {
        $packageDir = \dirname(__DIR__, 2).'/assets';
        $violations = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($packageDir, \FilesystemIterator::SKIP_DOTS),
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || !str_ends_with($file->getPathname(), '.css')) {
                continue;
            }

            $contents = (string) file_get_contents($file->getPathname());
            if (preg_match('/--sf-[a-z0-9-]+/i', $contents, $matches)) {
                $violations[] = str_replace($packageDir.'/', 'assets/', $file->getPathname()).': '.$matches[0];
            }
        }

        self::assertSame([], $violations, 'Use --ui-toolbar-* or --ui-profiler-* tokens in owned CSS — not --sf-*.');
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
