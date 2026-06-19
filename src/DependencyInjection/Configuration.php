<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('symfinity_ui_profiler');

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->scalarNode('theme')->defaultValue('default')->cannotBeEmpty()->end()
                ->scalarNode('toolbar_theme')->defaultNull()->end()
                ->scalarNode('asset_version')->defaultNull()->end()
                ->arrayNode('wdt')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('critical_css_inline')->defaultTrue()->end()
                        ->booleanNode('module_preload')->defaultTrue()->end()
                    ->end()
                ->end()
                ->booleanNode('legacy_sf_shim')
                    ->defaultFalse()
                    ->validate()
                        ->ifTrue()
                        ->thenInvalid('legacy_sf_shim is removed in symfinity 051 greenfield — MUST NOT enable.')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
