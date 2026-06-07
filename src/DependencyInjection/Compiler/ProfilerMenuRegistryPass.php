<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\DependencyInjection\Compiler;

use Symfinity\UiProfiler\Menu\ProfilerMenuRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ProfilerMenuRegistryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('ui_profiler.enabled') || !$container->getParameter('ui_profiler.enabled')) {
            return;
        }

        if (!$container->hasDefinition(ProfilerMenuRegistry::class)) {
            return;
        }

        /** @var array<string, array{title: string, icon: string, priority?: int}> $entries */
        $entries = $this->loadManifestMenuEntries($container);

        foreach ($container->findTaggedServiceIds('data_collector') as $serviceId => $dataCollectorTags) {
            if (!$container->hasDefinition($serviceId)) {
                continue;
            }

            $definition = $container->findDefinition($serviceId);
            $menuTags = $definition->getTag('ui_profiler.menu');
            if ([] === $menuTags) {
                continue;
            }

            $menuTag = $menuTags[0];
            $id = $menuTag['id'] ?? $dataCollectorTags[0]['id'] ?? null;
            if (!\is_string($id) || '' === $id) {
                continue;
            }

            $entries[$id] = [
                'title' => (string) ($menuTag['title'] ?? ''),
                'icon' => (string) ($menuTag['icon'] ?? ''),
                'priority' => (int) ($menuTag['priority'] ?? $dataCollectorTags[0]['priority'] ?? 0),
            ];
        }

        /** @var array<string, string> $stateHandlerIds */
        $stateHandlerIds = [];
        foreach ($container->findTaggedServiceIds('ui_profiler.menu_state') as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['collector']) || !\is_string($tag['collector']) || '' === $tag['collector']) {
                    continue;
                }
                $stateHandlerIds[$tag['collector']] = (string) $serviceId;
            }
        }

        $registryDefinition = $container->getDefinition(ProfilerMenuRegistry::class);
        $registryDefinition->setArgument('$entries', $entries);
        $registryDefinition->setArgument('$stateHandlerIds', $stateHandlerIds);

        $locatorServices = [];
        foreach ($stateHandlerIds as $serviceId) {
            $locatorServices[$serviceId] = new Reference($serviceId);
        }

        $container->getDefinition('ui_profiler.menu_state_locator')->setArgument(0, $locatorServices);
    }

    /**
     * @return array<string, array{title: string, icon: string, priority?: int}>
     */
    private function loadManifestMenuEntries(ContainerBuilder $container): array
    {
        $bundlePath = (string) $container->getParameter('ui_profiler.bundle_path');
        $manifestPath = $bundlePath.'/tests/Fixtures/symfony-collector-chrome-manifest.json';

        if (!is_file($manifestPath)) {
            return [];
        }

        /** @var array{menu_entries?: array<string, array{title?: string, icon?: string, priority?: int}>} $manifest */
        $manifest = json_decode((string) file_get_contents($manifestPath), true, 512, \JSON_THROW_ON_ERROR);

        $entries = [];
        foreach ($manifest['menu_entries'] ?? [] as $id => $config) {
            if (!\is_string($id) || !\is_array($config)) {
                continue;
            }

            $entries[$id] = [
                'title' => (string) ($config['title'] ?? ''),
                'icon' => (string) ($config['icon'] ?? ''),
                'priority' => (int) ($config['priority'] ?? 0),
            ];
        }

        return $entries;
    }
}
