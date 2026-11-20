<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfinity\UiProfiler\DependencyInjection\Compiler\AppendStimulusControllerPathPass;
use Symfinity\UiProfiler\DependencyInjection\UiProfilerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\UX\StimulusBundle\DependencyInjection\StimulusExtension;

/**
 * The profiler adds its controller folder to the app's controller paths; it never replaces them.
 */
final class AppendStimulusControllerPathPassTest extends TestCase
{
    private const APP_PATH = '%kernel.project_dir%/assets/controllers';

    #[Test]
    public function enabledProfilerAppendsItsPathAndKeepsTheAppDefault(): void
    {
        $container = $this->container(enabled: true);

        self::assertSame(
            [self::APP_PATH, realpath(\dirname(__DIR__, 3) . '/assets/controllers')],
            $container->getDefinition('stimulus.asset_mapper.controllers_map_generator')->getArgument(2),
        );
    }

    #[Test]
    public function disabledProfilerLeavesPathsAlone(): void
    {
        $container = $this->container(enabled: false);

        self::assertSame(
            [self::APP_PATH],
            $container->getDefinition('stimulus.asset_mapper.controllers_map_generator')->getArgument(2),
        );
    }

    #[Test]
    public function profilerNeverPrependsStimulusControllerPaths(): void
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new StimulusExtension());
        $extension = new UiProfilerExtension();
        $container->registerExtension($extension);
        $container->prependExtensionConfig('symfinity_ui_profiler', ['enabled' => true]);

        $extension->prepend($container);

        $withPaths = array_filter(
            $container->getExtensionConfig('stimulus'),
            static fn (array $config): bool => \array_key_exists('controller_paths', $config),
        );

        self::assertSame([], $withPaths);
    }

    #[Test]
    public function passIsANoOpWithoutStimulus(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('ui_profiler.enabled', true);
        $container->setParameter('ui_profiler.bundle_path', \dirname(__DIR__, 3));

        (new AppendStimulusControllerPathPass())->process($container);

        self::assertFalse($container->hasDefinition('stimulus.asset_mapper.controllers_map_generator'));
    }

    private function container(bool $enabled): ContainerBuilder
    {
        $container = new ContainerBuilder();
        (new UiProfilerExtension())->load([['enabled' => $enabled]], $container);
        $container->register('stimulus.asset_mapper.controllers_map_generator', \stdClass::class)
            ->setArguments([null, null, [self::APP_PATH], null]);

        (new AppendStimulusControllerPathPass())->process($container);
        (new AppendStimulusControllerPathPass())->process($container);

        return $container;
    }
}
