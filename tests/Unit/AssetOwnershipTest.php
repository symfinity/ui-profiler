<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class AssetOwnershipTest extends TestCase
{
    public function testNoStockWebProfilerIncludesInPackage(): void
    {
        $packageDir = \dirname(__DIR__, 2);
        $violations = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($packageDir, \FilesystemIterator::SKIP_DOTS),
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $path = $file->getPathname();
            if (!str_ends_with($path, '.twig') && !str_ends_with($path, '.html.twig')) {
                continue;
            }

            if (str_contains((string) file_get_contents($path), "{% include('@!WebProfiler")) {
                $violations[] = str_replace($packageDir.'/', '', $path);
            }
        }

        self::assertSame([], $violations, 'Remove @!WebProfiler includes — use owned assets per 044 asset-ownership contract.');
    }
}
