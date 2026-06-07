<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class UiProfilerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $packageDir = \dirname(__DIR__, 2);

        $loader = new YamlFileLoader($container, new FileLocator($packageDir.'/config'));
        $loader->load('services.yaml');

        $container->setParameter('ui_profiler.enabled', $config['enabled']);
        $container->setParameter('ui_profiler.bundle_path', $packageDir);
        $container->setParameter('ui_profiler.wdt.critical_inline', $config['wdt']['critical_inline']);
        $container->setParameter('ui_profiler.wdt.bootstrap_max_bytes', $config['wdt']['bootstrap_max_bytes']);
        $container->setParameter('ui_profiler.toolbar.dark_bar', $config['toolbar']['dark_bar']);
        $container->setParameter('ui_profiler.stimulus_enabled', false);

        if (!$config['enabled']) {
            return;
        }

        $container->prependExtensionConfig('twig', [
            'paths' => [
                $packageDir.'/templates' => 'UiProfiler',
            ],
        ]);

        $container->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    $packageDir.'/assets' => 'ui-profiler',
                ],
            ],
        ]);
    }

    public function getAlias(): string
    {
        return 'ui_profiler';
    }
}
