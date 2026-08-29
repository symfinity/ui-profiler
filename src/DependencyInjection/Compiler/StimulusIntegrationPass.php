<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/** Sets ui_profiler.stimulus_enabled when symfony/stimulus-bundle is registered. */
final class StimulusIntegrationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('ui_profiler.enabled')) {
            return;
        }

        $enabled = (bool) $container->getParameter('ui_profiler.enabled')
            && $container->hasExtension('stimulus');

        $container->setParameter('ui_profiler.stimulus_enabled', $enabled);
    }
}
