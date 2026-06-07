<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\UX\StimulusBundle\StimulusBundle;

/**
 * Enables Stimulus wiring only when StimulusBundle is registered (051 DI-2 / DI-4).
 */
final class StimulusAvailabilityPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('ui_profiler.enabled') || !$container->getParameter('ui_profiler.enabled')) {
            $container->setParameter('ui_profiler.stimulus_enabled', false);

            return;
        }

        /** @var array<string, class-string> $bundles */
        $bundles = $container->getParameter('kernel.bundles');
        $enabled = \in_array(StimulusBundle::class, $bundles, true);

        $container->setParameter('ui_profiler.stimulus_enabled', $enabled);

        if (!$enabled || !$container->hasParameter('ui_profiler.bundle_path')) {
            return;
        }

        $bundlePath = (string) $container->getParameter('ui_profiler.bundle_path');
        $controllerPath = $bundlePath.'/assets/controllers';

        if (!is_dir($controllerPath)) {
            return;
        }

        $container->prependExtensionConfig('stimulus', [
            'controller_paths' => [$controllerPath],
        ]);
    }
}
