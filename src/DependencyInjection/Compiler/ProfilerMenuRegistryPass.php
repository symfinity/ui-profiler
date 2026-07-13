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

            $menuTag = $menuTags[0] ?? null;
            if (!\is_array($menuTag)) {
                continue;
            }

            $collectorTag = $dataCollectorTags[0] ?? null;
            if (!\is_array($collectorTag)) {
                $collectorTag = [];
            }

            $id = $menuTag['id'] ?? $collectorTag['id'] ?? null;
            if (!\is_string($id) || '' === $id) {
                continue;
            }

            $priority = $menuTag['priority'] ?? $collectorTag['priority'] ?? 0;

            $entries[$id] = [
                'title' => $this->stringValue($menuTag['title'] ?? ''),
                'icon' => $this->stringValue($menuTag['icon'] ?? ''),
                'priority' => $this->intValue($priority),
            ];
        }

        /** @var array<string, string> $stateHandlerIds */
        $stateHandlerIds = [];
        foreach ($container->findTaggedServiceIds('ui_profiler.menu_state') as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (!\is_array($tag)) {
                    continue;
                }

                $collector = $tag['collector'] ?? null;
                if (!\is_string($collector) || '' === $collector) {
                    continue;
                }

                $stateHandlerIds[$collector] = (string) $serviceId;
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
        if (!$container->hasParameter('ui_profiler.bundle_path')) {
            return [];
        }

        $bundlePathParam = $container->getParameter('ui_profiler.bundle_path');
        if (!\is_string($bundlePathParam) || '' === $bundlePathParam) {
            return [];
        }

        $manifestPath = $bundlePathParam.'/tests/Fixtures/symfony-collector-chrome-manifest.json';

        if (!is_file($manifestPath)) {
            return [];
        }

        /** @var array{menu_entries?: array<string, array{title?: string, icon?: string, priority?: int}>} $manifest */
        $manifest = json_decode((string) file_get_contents($manifestPath), true, 512, \JSON_THROW_ON_ERROR);

        $entries = [];
        foreach ($manifest['menu_entries'] ?? [] as $id => $config) {
            $entries[$id] = [
                'title' => $this->stringValue($config['title'] ?? ''),
                'icon' => $this->stringValue($config['icon'] ?? ''),
                'priority' => $this->intValue($config['priority'] ?? 0),
            ];
        }

        return $entries;
    }

    private function stringValue(mixed $value): string
    {
        return \is_string($value) ? $value : (is_scalar($value) ? (string) $value : '');
    }

    private function intValue(mixed $value): int
    {
        return \is_int($value) ? $value : (is_numeric($value) ? (int) $value : 0);
    }
}
