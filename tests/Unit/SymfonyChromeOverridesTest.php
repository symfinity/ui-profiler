<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfinity\UiProfiler\DependencyInjection\Compiler\PrependWebProfilerTemplatesPass;

final class SymfonyChromeOverridesTest extends TestCase
{
    private const MANIFEST_PATH = __DIR__.'/../../config/profiler-menu-manifest.json';

    public function testManifestBundlesHaveChromeDirectories(): void
    {
        /** @var array{bundles: list<array{bundle: string, collectors: list<string>}>} $manifest */
        $manifest = json_decode((string) file_get_contents(self::MANIFEST_PATH), true, 512, \JSON_THROW_ON_ERROR);
        $packageRoot = \dirname(__DIR__, 2);

        foreach ($manifest['bundles'] as $entry) {
            $chromeDir = $packageRoot.'/templates/chrome/'.$entry['bundle'];
            self::assertDirectoryExists($chromeDir, sprintf('Missing chrome override dir for %s', $entry['bundle']));

            foreach ($entry['collectors'] as $collector) {
                $matches = array_merge(
                    glob($chromeDir.'/Collector/'.$collector.'.html.twig') ?: [],
                    glob($chromeDir.'/Profiler/'.$collector.'.html.twig') ?: [],
                );
                self::assertNotEmpty(
                    $matches,
                    sprintf('Missing chrome override for %s collector %s', $entry['bundle'], $collector),
                );
            }
        }
    }

    public function testMenuBlocksUseCollectorMenuLabelPartial(): void
    {
        $packageRoot = \dirname(__DIR__, 2);
        $chromeDir = $packageRoot.'/templates/chrome';
        $manifestMenuIds = $this->manifestMenuEntryIds();

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($chromeDir, \FilesystemIterator::SKIP_DOTS),
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.html.twig')) {
                continue;
            }

            $content = (string) file_get_contents($file->getPathname());
            if (!preg_match('/{% block menu %}.*?{% endblock %}/s', $content, $match)) {
                continue;
            }

            $menuBlock = $match[0];
            $relative = str_replace($packageRoot.'/', '', $file->getPathname());

            foreach ($manifestMenuIds as $menuId) {
                if (str_contains($relative, '/'.$menuId.'.html.twig')) {
                    self::fail(sprintf(
                        'Collector %s uses manifest menu registry — remove legacy menu block from %s',
                        $menuId,
                        $relative,
                    ));
                }
            }

            self::assertStringContainsString(
                'collector-menu-label.html.twig',
                $menuBlock,
                sprintf('Menu block must use collector-menu-label partial: %s', $relative),
            );
            self::assertStringNotContainsString(
                'class="label',
                $menuBlock,
                sprintf('Menu block must not use legacy .label markup: %s', $relative),
            );
        }
    }

    public function testManifestMenuEntriesHaveRequiredFields(): void
    {
        /** @var array{menu_entries?: array<string, array{title?: string, icon?: string}>} $manifest */
        $manifest = json_decode((string) file_get_contents(self::MANIFEST_PATH), true, 512, \JSON_THROW_ON_ERROR);

        foreach ($manifest['menu_entries'] ?? [] as $id => $entry) {
            self::assertNotSame('', $entry['title'] ?? '', sprintf('menu_entries.%s.title required', $id));
            self::assertNotSame('', $entry['icon'] ?? '', sprintf('menu_entries.%s.icon required', $id));
        }
    }

    /**
     * @return list<string>
     */
    private function manifestMenuEntryIds(): array
    {
        /** @var array{menu_entries?: array<string, mixed>} $manifest */
        $manifest = json_decode((string) file_get_contents(self::MANIFEST_PATH), true, 512, \JSON_THROW_ON_ERROR);

        return array_keys($manifest['menu_entries'] ?? []);
    }

    public function testDiscoverOverrideBundlesMatchesManifest(): void
    {
        /** @var array{bundles: list<array{bundle: string}>} $manifest */
        $manifest = json_decode((string) file_get_contents(self::MANIFEST_PATH), true, 512, \JSON_THROW_ON_ERROR);
        $expected = array_map(static fn (array $entry): string => $entry['bundle'], $manifest['bundles']);
        sort($expected);

        $discovered = PrependWebProfilerTemplatesPass::discoverOverrideBundles(\dirname(__DIR__, 2));
        self::assertSame($expected, $discovered);
    }
}
