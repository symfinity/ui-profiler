<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\DependencyInjection\Compiler;

use Symfinity\UiProfiler\DataCollector\UiKernelDataCollector;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterUiKernelCollectorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $collectorId = UiKernelDataCollector::class;
        if (!$container->hasDefinition($collectorId)) {
            return;
        }

        if (!$container->getParameter('kernel.debug')) {
            $container->removeDefinition($collectorId);

            return;
        }

        if (!class_exists(WebProfilerBundle::class)) {
            $container->removeDefinition($collectorId);
        }
    }
}
