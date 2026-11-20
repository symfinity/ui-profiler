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
        // Stimulus controller path: appended by AppendStimulusControllerPathPass, never prepended
        // as stimulus.controller_paths config (that would drop the app's own controllers folder).
        // Asset mapper paths must be prepended here. framework is already loaded by the time load() runs.
        $configs = $container->getExtensionConfig($this->getAlias());
        if ($configs === []) {
            return;
        }

        $config = $this->processConfiguration(new Configuration(), $configs);
        if (!$config['enabled']) {
            return;
        }

        $assets = \dirname(__DIR__, 2).'/assets';
        $mapped = realpath($assets);
        $container->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    (false !== $mapped ? $mapped : $assets) => 'ui-profiler',
                ],
            ],
        ]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        /** @var array{enabled: bool, theme: string, toolbar_theme: string|null, asset_version: string|null, wdt: array{critical_css_inline: bool, module_preload: bool}, legacy_sf_shim: bool} $config */
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
        }
    }

    public function getAlias(): string
    {
        return 'symfinity_ui_profiler';
    }
}
