<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class UiProfilerExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('stimulus') || !$this->isEnabled($container)) {
            return;
        }

        $packageDir = \dirname(__DIR__, 2);

        $container->prependExtensionConfig('stimulus', [
            'controller_paths' => [
                $packageDir . '/assets/controllers',
            ],
        ]);
    }

    private function isEnabled(ContainerBuilder $container): bool
    {
        foreach ($container->getExtensionConfig($this->getAlias()) as $config) {
            if (($config['enabled'] ?? false) === true) {
                return true;
            }
        }

        return false;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $packageDir = \dirname(__DIR__, 2);

        $loader = new YamlFileLoader($container, new FileLocator($packageDir . '/config'));
        $loader->load('services.yaml');

        $container->setParameter('ui_profiler.enabled', $config['enabled']);
        $container->setParameter('ui_profiler.theme', $config['theme']);
        $container->setParameter('ui_profiler.toolbar_theme', $config['toolbar_theme']);
        $container->setParameter('ui_profiler.asset_version', $config['asset_version']);
        $container->setParameter('ui_profiler.bundle_path', $packageDir);
        $container->setParameter('ui_profiler.wdt.critical_css_inline', $config['wdt']['critical_css_inline']);
        $container->setParameter('ui_profiler.wdt.module_preload', $config['wdt']['module_preload']);
        $container->setParameter('ui_profiler.legacy_sf_shim', $config['legacy_sf_shim']);
        $container->setParameter('ui_profiler.stimulus_enabled', false);

        if ($config['enabled']) {
            // @UiProfiler alias; WebProfiler overrides use PrependWebProfilerTemplatesPass.
            $container->prependExtensionConfig('twig', [
                'paths' => [
                    $packageDir . '/templates' => 'UiProfiler',
                ],
            ]);

            $container->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        $packageDir . '/assets' => 'ui-profiler',
                    ],
                ],
            ]);
        }
    }

    public function getAlias(): string
    {
        return 'ui_profiler';
    }
}
