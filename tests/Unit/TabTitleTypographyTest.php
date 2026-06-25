<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/** symfinity 122 T028 — tab panel titles delegate typography to ux-blocks-core */
final class TabTitleTypographyTest extends TestCase
{
    public function testTabTitlesDeclareTypographyRole(): void
    {
        $packageDir = \dirname(__DIR__, 2);
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($packageDir.'/templates/chrome', \FilesystemIterator::SKIP_DOTS),
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.html.twig')) {
                continue;
            }

            $content = (string) file_get_contents($file->getPathname());
            if (!str_contains($content, 'tab-title')) {
                continue;
            }

            $relative = str_replace($packageDir.'/', '', $file->getPathname());
            preg_match_all('/<h([23]) class="tab-title"([^>]*)>/', $content, $matches, \PREG_SET_ORDER);

            foreach ($matches as $match) {
                $attrs = $match[2];
                self::assertStringContainsString(
                    'data-ui-role="typography"',
                    $attrs,
                    sprintf('tab-title must declare typography role: %s', $relative),
                );
                self::assertStringContainsString(
                    'data-ui-variant="h'.$match[1].'"',
                    $attrs,
                    sprintf('tab-title must declare h%s typography variant: %s', $match[1], $relative),
                );
            }
        }
    }
}
