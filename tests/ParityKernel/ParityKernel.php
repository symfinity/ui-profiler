<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\ParityKernel;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfinity\UiKernel\UiKernelBundle;
use Symfinity\UiProfiler\UiProfilerBundle;
use Symfinity\UxBlocksCore\SymfinityUxBlocksCoreBundle;
use Symfinity\UxBlocksExtended\SymfinityUxBlocksExtendedBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\UX\TwigComponent\TwigComponentBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ParityKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new SecurityBundle(),
            new DoctrineBundle(),
            new UiKernelBundle(),
            new TwigComponentBundle(),
            new SymfinityUxBlocksCoreBundle(),
            new SymfinityUxBlocksExtendedBundle(),
            new WebProfilerBundle(),
            new UiProfilerBundle(),
        ];
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('@WebProfilerBundle/Resources/config/routing/wdt.php')->prefix('/_wdt');
        $routes->import('@WebProfilerBundle/Resources/config/routing/profiler.php')->prefix('/_profiler');
        $routes->import(\dirname(__DIR__, 2).'/config/routes.yaml');

        $routes->add('parity_home', '/')->controller([ParityController::class, 'home']);
        $routes->add('parity_hostile_css', '/parity/hostile-css')->controller([ParityController::class, 'hostileCss']);
        $routes->add('parity_exception', '/parity/exception')->controller([ParityController::class, 'exception']);
        $routes->add('parity_ajax', '/parity/ajax')->controller([ParityController::class, 'ajax']);
        $routes->add('parity_rich', '/parity/rich')->controller([ParityController::class, 'rich']);
        $routes->add('parity_form_error', '/parity/form-error')
            ->controller([ParityController::class, 'formError'])
            ->methods(['GET', 'POST']);
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $fixtureDir = __DIR__;

        $container->extension('framework', [
            'secret' => 'test-secret',
            'test' => true,
            'asset_mapper' => [
                'paths' => [],
            ],
            'router' => ['utf8' => true],
            'php_errors' => ['log' => false],
            'profiler' => [
                'collect' => true,
                'only_exceptions' => false,
                'collect_serializer_data' => true,
            ],
            'form' => true,
            'validation' => ['enabled' => true],
            'csrf_protection' => false,
            'http_client' => true,
            'serializer' => ['enabled' => true],
            'default_locale' => 'en',
            'translator' => [
                'enabled' => true,
                'paths' => [$fixtureDir . '/translations'],
            ],
            'mailer' => [
                'dsn' => 'null://null',
            ],
            'messenger' => [
                'transports' => [
                    'sync' => 'sync://',
                ],
                'routing' => [
                    ParityMessage::class => 'sync',
                ],
            ],
            'cache' => [
                'pools' => [
                    'parity.cache' => ['adapter' => 'cache.adapter.array'],
                ],
            ],
            'session' => [
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
        ]);

        $container->extension('twig', [
            'paths' => [
                $fixtureDir . '/templates' => 'parity',
            ],
        ]);

        $container->extension('doctrine', [
            'dbal' => [
                'driver' => 'pdo_sqlite',
                'path' => '%kernel.cache_dir%/parity.sqlite',
            ],
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
            'enabled' => true,
        ]);

        $container->extension('symfinity_ui_kernel', [
            'default_theme' => 'default',
        ]);

        $services = $container->services()
            ->set(ParityController::class)
            ->public()
            ->autowire()
            ->autoconfigure()
            ->tag('controller.service_arguments')
            ->set(ParityMessageHandler::class)
            ->autoconfigure()
            ->set(HttpClientInterface::class, ParityTestHttpClient::class);

        if (class_exists(\Symfony\Component\Form\Form::class)) {
            $services
                ->set('twig.extension.form', \Symfony\Bridge\Twig\Extension\FormExtension::class)
                ->args([service('translator')->nullOnInvalid()])
                ->set('twig.form.engine', \Symfony\Bridge\Twig\Form\TwigRendererEngine::class)
                ->args([['form_div_layout.html.twig'], service('twig')])
                ->set('twig.form.renderer', \Symfony\Component\Form\FormRenderer::class)
                ->args([service('twig.form.engine'), service('security.csrf.token_manager')->nullOnInvalid()])
                ->tag('twig.runtime');
        }
    }
}
