<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Appends the profiler's `assets/controllers` to StimulusBundle's controller paths.
 *
 * Prepending `stimulus.controller_paths` config would replace the app default
 * (`%kernel.project_dir%/assets/controllers`), so app controllers would stop loading.
 */
final class AppendStimulusControllerPathPass implements CompilerPassInterface
{
    private const GENERATOR = 'stimulus.asset_mapper.controllers_map_generator';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::GENERATOR)
            || !$container->hasParameter('ui_profiler.enabled')
            || true !== $container->getParameter('ui_profiler.enabled')
        ) {
            return;
        }

        $bundlePath = $container->getParameter('ui_profiler.bundle_path');
        if (!\is_string($bundlePath)) {
            return;
        }

        $controllerPath = $bundlePath . '/assets/controllers';
        $realControllerPath = realpath($controllerPath);
        if (false !== $realControllerPath) {
            $controllerPath = $realControllerPath;
        }
        $definition = $container->getDefinition(self::GENERATOR);
        $paths = $definition->getArgument(2);
        if (!\is_array($paths)) {
            $paths = [];
        }

        if (!\in_array($controllerPath, $paths, true)) {
            $paths[] = $controllerPath;
        }

        $definition->replaceArgument(2, $paths);
    }
}
