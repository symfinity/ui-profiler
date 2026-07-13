<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\ParityKernel;

use Symfinity\UiKernel\UiKernelBundle;
use Symfinity\UiProfiler\UiProfilerBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/** Fixture kernel with ui-profiler chrome disabled — stock Web Profiler shell expected. */
final class DisabledParityKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new SecurityBundle(),
            new UiKernelBundle(),
            new WebProfilerBundle(),
            new UiProfilerBundle(),
        ];
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('@WebProfilerBundle/Resources/config/routing/wdt.php')->prefix('/_wdt');
        $routes->import('@WebProfilerBundle/Resources/config/routing/profiler.php')->prefix('/_profiler');

        $routes->add('parity_home', '/')->controller([ParityController::class, 'home']);
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'secret' => 'test-secret',
            'test' => true,
            'router' => ['utf8' => true],
            'php_errors' => ['log' => false],
            'profiler' => ['collect' => true, 'only_exceptions' => false],
            'form' => false,
            'csrf_protection' => false,
        ]);

        $container->extension('twig', [
            'form_themes' => [],
        ]);

        $container->extension('security', [
            'firewalls' => [
                'dev' => [
                    'pattern' => '^/(_profiler|_wdt)',
                    'security' => false,
                ],
                'main' => [
                    'lazy' => true,
                ],
            ],
        ]);

        $container->extension('web_profiler', [
            'toolbar' => true,
            'intercept_redirects' => false,
        ]);

        $container->extension('symfinity_ui_profiler', [
            'enabled' => false,
        ]);

        $container->extension('symfinity_ui_kernel', [
            'default_theme' => 'default',
        ]);

        $container->services()
            ->set(ParityController::class)
            ->public()
            ->autowire()
            ->autoconfigure();
    }
}
