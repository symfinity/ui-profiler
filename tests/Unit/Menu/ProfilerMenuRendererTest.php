<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit\Menu;

use PHPUnit\Framework\TestCase;
use Symfinity\UiProfiler\Menu\ProfilerMenuRegistry;
use Symfinity\UiProfiler\Menu\ProfilerMenuRenderer;
use Symfinity\UiProfiler\Menu\ProfilerMenuStateHandler;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class ProfilerMenuRendererTest extends TestCase
{
    public function testRendersRegistryMenuWithStateHandler(): void
    {
        $registry = new ProfilerMenuRegistry(
            [
                'demo' => [
                    'title' => 'Demo Collector',
                    'icon' => '@Demo/icon.svg',
                ],
            ],
            ['demo' => 'demo.handler'],
        );

        $handler = new class implements ProfilerMenuStateHandler {
            public function disabled(object $collector): bool
            {
                return true;
            }

            public function badge(object $collector): ?array
            {
                return ['count' => 3, 'status' => 'warning'];
            }
        };

        $locator = new ServiceLocator(['demo.handler' => static fn (): ProfilerMenuStateHandler => $handler]);

        $twig = new Environment(new ArrayLoader([
            '@UiProfiler/_chrome/partials/collector-menu-label.html.twig' => '<span data-ui-profiler-menu-label{% if disabled %} class="disabled"{% endif %}>{{ title }}-{{ count }}</span>',
            '@Demo/icon.svg' => '<svg></svg>',
        ]));

        $renderer = new ProfilerMenuRenderer($registry, $twig, $locator, true);

        $html = $renderer->render('demo', new \stdClass());

        self::assertStringContainsString('data-ui-profiler-menu-label', $html);
        self::assertStringContainsString('Demo Collector-3', $html);
        self::assertStringContainsString('disabled', $html);
    }

    public function testResolvesNamespacedIconViaTwigLoader(): void
    {
        $registry = new ProfilerMenuRegistry([
            'demo' => [
                'title' => 'Demo',
                'icon' => '@Demo/icon.svg',
            ],
        ]);

        $twig = new Environment(new ArrayLoader([
            '@Demo/icon.svg' => '<svg data-demo-icon="1"></svg>',
            '@UiProfiler/_chrome/partials/collector-menu-label.html.twig' => '<span data-ui-profiler-menu-icon>{{ icon|raw }}</span>',
        ]));

        $renderer = new ProfilerMenuRenderer($registry, $twig, new ServiceLocator([]), true);

        $html = $renderer->render('demo', new \stdClass());

        self::assertStringContainsString('data-demo-icon="1"', $html);
    }

    public function testReturnsEmptyWhenRegistryMissesCollector(): void
    {
        $registry = new ProfilerMenuRegistry([]);
        $twig = new Environment(new ArrayLoader([]));
        $locator = new ServiceLocator([]);

        $renderer = new ProfilerMenuRenderer($registry, $twig, $locator, true);

        self::assertSame('', $renderer->render('missing', new \stdClass()));
    }

    public function testReturnsEmptyWhenDisabled(): void
    {
        $registry = new ProfilerMenuRegistry([
            'demo' => ['title' => 'Demo', 'icon' => '@Demo/icon.svg'],
        ]);
        $twig = new Environment(new ArrayLoader([]));
        $locator = new ServiceLocator([]);

        $renderer = new ProfilerMenuRenderer($registry, $twig, $locator, false);

        self::assertSame('', $renderer->render('demo', new \stdClass()));
    }
}
