<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler;

use Symfinity\UiProfiler\DependencyInjection\Compiler\AppendStimulusControllerPathPass;
use Symfinity\UiProfiler\DependencyInjection\Compiler\PrependWebProfilerTemplatesPass;
use Symfinity\UiProfiler\DependencyInjection\Compiler\ProfilerMenuRegistryPass;
use Symfinity\UiProfiler\DependencyInjection\Compiler\RegisterUiKernelCollectorPass;
use Symfinity\UiProfiler\DependencyInjection\Compiler\StimulusIntegrationPass;
use Symfinity\UiProfiler\DependencyInjection\UiProfilerExtension;
use Symfony\Bundle\TwigBundle\DependencyInjection\Configurator\TwigConfigurator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class UiProfilerBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new PrependWebProfilerTemplatesPass());
        $container->addCompilerPass(new ProfilerMenuRegistryPass());
        // Priority 1: drop the collector before FrameworkBundle's ProfilerPass (priority 0) wires it into the profiler.
        $container->addCompilerPass(new RegisterUiKernelCollectorPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        $container->addCompilerPass(new StimulusIntegrationPass());
        $container->addCompilerPass(new AppendStimulusControllerPathPass());
    }

    public function getContainerExtension(): ExtensionInterface
    {
        return new UiProfilerExtension();
    }

    public function configureTwig(TwigConfigurator $configurator): void
    {
        $configurator->path($this->getPath() . '/templates', 'UiProfiler');
    }

    public function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getPath() . '/config/routes.yaml');
    }
}
