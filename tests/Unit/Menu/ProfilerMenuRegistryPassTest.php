<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit\Menu;

use PHPUnit\Framework\TestCase;
use Symfinity\UiProfiler\DependencyInjection\Compiler\ProfilerMenuRegistryPass;
use Symfinity\UiProfiler\Menu\ProfilerMenuRegistry;
use Symfinity\UiProfiler\Menu\State\DebugMenuStateHandler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class ProfilerMenuRegistryPassTest extends TestCase
{
    public function testCollectsTaggedMenuMetadataAndManifestEntries(): void
    {
        $container = new ContainerBuilder();
        $packageDir = \dirname(__DIR__, 3);

        $container->setParameter('ui_profiler.enabled', true);
        $container->setParameter('ui_profiler.bundle_path', $packageDir);
        $container->register(ProfilerMenuRegistry::class)->setAutowired(false);
        $container->register('ui_profiler.menu_state_locator', \Symfony\Component\DependencyInjection\ServiceLocator::class)
            ->setAutowired(false)
            ->setArgument(0, []);

        $container->register('app.ui_kernel_collector', Definition::class)
            ->addTag('data_collector', ['id' => 'ui_kernel'])
            ->addTag('ui_profiler.menu', ['title' => 'UI Kernel', 'icon' => '@UiKernel/Collector/icon.svg']);

        $container->register(DebugMenuStateHandler::class)
            ->addTag('ui_profiler.menu_state', ['collector' => 'dump']);

        (new ProfilerMenuRegistryPass())->process($container);

        $registryDef = $container->getDefinition(ProfilerMenuRegistry::class);
        /** @var array<string, array{title: string, icon: string}> $entries */
        $entries = $registryDef->getArgument('$entries');

        self::assertSame('UI Kernel', $entries['ui_kernel']['title']);
        self::assertSame('@UiKernel/Collector/icon.svg', $entries['ui_kernel']['icon']);
        self::assertSame('Mercure', $entries['mercure']['title']);
        self::assertSame('Debug', $entries['dump']['title']);

        /** @var array<string, string> $handlerIds */
        $handlerIds = $registryDef->getArgument('$stateHandlerIds');
        self::assertArrayHasKey('dump', $handlerIds);
    }

    public function testSkipsWhenDisabled(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('ui_profiler.enabled', false);
        $container->register(ProfilerMenuRegistry::class)->setAutowired(false);

        (new ProfilerMenuRegistryPass())->process($container);

        self::assertSame([], $container->getDefinition(ProfilerMenuRegistry::class)->getArguments());
    }
}
