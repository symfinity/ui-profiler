<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfinity\UiProfiler\DependencyInjection\UiProfilerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class UiProfilerExtensionTest extends TestCase
{
    public function testEnabledRegistersUiProfilerTwigAlias(): void
    {
        $container = new ContainerBuilder();
        $extension = new UiProfilerExtension();
        $extension->load([['enabled' => true]], $container);

        $extensions = $container->getExtensionConfig('twig');
        self::assertNotEmpty($extensions);

        /** @var array{paths?: array<string, string>} $twigConfig */
        $twigConfig = $extensions[0];
        $paths = $twigConfig['paths'] ?? [];
        $matched = false;
        foreach ($paths as $path => $namespace) {
            if ('UiProfiler' === $namespace && str_contains($path, 'packages/ui-profiler/templates')) {
                $matched = true;
                break;
            }
        }

        self::assertTrue($matched, 'Expected @UiProfiler alias path when enabled');
        self::assertTrue($container->getParameter('ui_profiler.enabled'));

        $frameworkConfigs = $container->getExtensionConfig('framework');
        self::assertNotEmpty($frameworkConfigs);
        /** @var array{asset_mapper?: array{paths?: array<string, string>}} $frameworkConfig */
        $frameworkConfig = $frameworkConfigs[0];
        $assetPaths = $frameworkConfig['asset_mapper']['paths'] ?? [];
        self::assertContains('ui-profiler', $assetPaths);
    }

    public function testDisabledSkipsTwigOverridePath(): void
    {
        $container = new ContainerBuilder();
        $extension = new UiProfilerExtension();
        $extension->load([['enabled' => false]], $container);

        self::assertSame([], $container->getExtensionConfig('twig'));
        self::assertFalse($container->getParameter('ui_profiler.enabled'));
    }
}
