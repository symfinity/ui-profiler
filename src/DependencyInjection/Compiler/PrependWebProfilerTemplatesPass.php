<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Prepends ui-profiler Twig overrides ahead of stock bundle views when enabled.
 */
final class PrependWebProfilerTemplatesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('ui_profiler.enabled') || !$container->getParameter('ui_profiler.enabled')) {
            return;
        }

        if (!$container->hasDefinition('twig.loader.native_filesystem')) {
            return;
        }

        $bundlePath = (string) $container->getParameter('ui_profiler.bundle_path');
        $loader = $container->getDefinition('twig.loader.native_filesystem');

        foreach (self::discoverOverrideBundles($bundlePath) as $bundleName) {
            $overridePath = $bundlePath.'/templates/chrome/'.$bundleName;

            if (!is_dir($overridePath)) {
                continue;
            }

            $loader->addMethodCall('prependPath', [$overridePath, str_replace('Bundle', '', $bundleName)]);
        }
    }

    /**
     * @return list<string>
     */
    public static function discoverOverrideBundles(string $packageRoot): array
    {
        $chromeDir = $packageRoot.'/templates/chrome';
        if (!is_dir($chromeDir)) {
            return [];
        }

        $bundles = [];
        foreach (scandir($chromeDir) ?: [] as $entry) {
            if ('.' === $entry || '..' === $entry) {
                continue;
            }
            if (is_dir($chromeDir.'/'.$entry) && str_ends_with($entry, 'Bundle')) {
                $bundles[] = $entry;
            }
        }

        sort($bundles);

        return $bundles;
    }
}
