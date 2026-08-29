<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfinity\UiProfiler\DependencyInjection\Compiler\PrependWebProfilerTemplatesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PrependWebProfilerTemplatesPassTest extends TestCase
{
    public function testPrependsDiscoveredOverridePathsWhenEnabled(): void
    {
        $container = new ContainerBuilder();
        $packageDir = \dirname(__DIR__, 2);

        $container->setParameter('ui_profiler.enabled', true);
        $container->setParameter('ui_profiler.bundle_path', $packageDir);
        $container->register('twig.loader.native_filesystem', \stdClass::class);

        (new PrependWebProfilerTemplatesPass())->process($container);

        $expectedBundles = PrependWebProfilerTemplatesPass::discoverOverrideBundles($packageDir);
        self::assertNotEmpty($expectedBundles);

        $calls = $container->getDefinition('twig.loader.native_filesystem')->getMethodCalls();
        self::assertCount(\count($expectedBundles), $calls);

        /** @var list<string> $paths */
        $paths = [];
        foreach ($calls as $call) {
            self::assertIsArray($call);
            self::assertArrayHasKey(1, $call);
            $args = $call[1];
            self::assertIsArray($args);
            self::assertIsString($args[0] ?? null);
            $paths[] = $args[0];
        }
        foreach ($expectedBundles as $bundleName) {
            self::assertTrue(
                array_any($paths, static fn (string $path): bool => str_contains($path, 'templates/chrome/'.$bundleName)),
                sprintf('Expected prepend path for %s', $bundleName),
            );
        }
    }

    public function testSkipsWhenDisabled(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('ui_profiler.enabled', false);
        $container->register('twig.loader.native_filesystem', \stdClass::class);

        (new PrependWebProfilerTemplatesPass())->process($container);

        self::assertSame([], $container->getDefinition('twig.loader.native_filesystem')->getMethodCalls());
    }
}
