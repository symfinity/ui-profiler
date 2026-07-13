<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Enforces 051 greenfield markup — no archive sf-* vocabulary in owned templates/assets.
 *
 * @see docs/greenfield-implement-rules.md
 */
#[Group('ui-profiler-parity')]
final class GreenfieldMarkupTest extends TestCase
{
    /** @var list<string> */
    private const FORBIDDEN_PANEL_PATTERNS = [
        'sf-tabs',
        'sf-toggle',
        'sf-reset',
        'table-with-search-field',
        'class="sf-',
        "class='sf-",
    ];

    /** @var list<string> */
    private const FORBIDDEN_INLINE_CSS_PATTERNS = [
        '.sf-toolbar',
        '.sf-tabs',
        '.sf-toggle',
        '.sf-profiler-timeline',
        '.sf-serializer',
        '.sf-validator',
        '--sf-',
        '--base-0',
        'var(--border)',
        'var(--color-error)',
    ];

    public function testOwnedTwigDoesNotUseArchiveSfVocabulary(): void
    {
        $packageDir = \dirname(__DIR__, 2);
        $violations = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($packageDir.'/templates', \FilesystemIterator::SKIP_DOTS),
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.twig')) {
                continue;
            }

            $relative = str_replace($packageDir.'/', '', $file->getPathname());
            if (str_starts_with($relative, 'templates/chrome/README.md')) {
                continue;
            }

            $contents = (string) file_get_contents($file->getPathname());
            $panelContents = $this->stripDeferredTwigBlocks($contents);

            foreach (self::FORBIDDEN_PANEL_PATTERNS as $pattern) {
                if (str_contains($panelContents, $pattern)) {
                    $violations[] = sprintf('%s: forbidden %s — rebuild on ux-blocks (051 GC-N2)', $relative, $pattern);
                }
            }
        }

        self::assertSame([], $violations);
    }

    public function testOwnedJsDoesNotUseArchiveSfSelectors(): void
    {
        $assetsDir = \dirname(__DIR__, 2).'/assets';
        if (!is_dir($assetsDir)) {
            self::addToAssertionCount(1);

            return;
        }

        $violations = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($assetsDir, \FilesystemIterator::SKIP_DOTS),
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.js')) {
                continue;
            }

            $relative = str_replace($assetsDir.'/', 'assets/', $file->getPathname());

            $contents = (string) file_get_contents($file->getPathname());
            if (preg_match('/\.sf-toolbar|sf-ajax-request|querySelector\([\'"].*sf-/', $contents)) {
                $violations[] = $relative;
            }
        }

        self::assertSame([], $violations, 'WDT/shell JS must target data-ui-profiler-* hooks — not archive sf-* selectors.');
    }

    public function testOwnedTwigInlineStylesDoNotUseForbiddenLegacySelectors(): void
    {
        $packageDir = \dirname(__DIR__, 2);
        $violations = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($packageDir.'/templates', \FilesystemIterator::SKIP_DOTS),
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.twig')) {
                continue;
            }

            $relative = str_replace($packageDir.'/', '', $file->getPathname());
            $contents = (string) file_get_contents($file->getPathname());

            if (!preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $contents, $styleBlocks)) {
                continue;
            }

            foreach ($styleBlocks[1] as $styleBody) {
                foreach (self::FORBIDDEN_INLINE_CSS_PATTERNS as $pattern) {
                    if (str_contains($styleBody, $pattern)) {
                        $violations[] = sprintf('%s: forbidden inline CSS pattern %s', $relative, $pattern);
                    }
                }
            }
        }

        self::assertSame([], $violations);
    }

    private function stripDeferredTwigBlocks(string $contents): string
    {
        $stripped = $this->removeTwigBlock($contents, 'toolbar');
        $stripped = $this->removeTwigBlock($stripped, 'head');

        return $stripped;
    }

    private function removeTwigBlock(string $contents, string $blockName): string
    {
        $pattern = '/\{%\s*block\s+'.preg_quote($blockName, '/').'\s*%\}.*?\{%\s*endblock\s*%\}/s';

        return preg_replace($pattern, '', $contents) ?? $contents;
    }
}
