<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ui_profiler');

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->arrayNode('wdt')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('critical_inline')->defaultTrue()->end()
                        ->integerNode('bootstrap_max_bytes')->defaultValue(5120)->min(1024)->end()
                    ->end()
                ->end()
                ->arrayNode('toolbar')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('dark_bar')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end()
            ->validate()
                ->ifTrue(static fn (array $config): bool => \array_key_exists('legacy_sf_shim', $config))
                ->thenInvalid('ui_profiler.legacy_sf_shim is removed in greenfield 051 — zero owned CSS only.')
            ->end();

        return $treeBuilder;
    }
}
