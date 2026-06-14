<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfinity\UiKernel\Contract\Catalog\GraphVariantCatalogPort;
use Symfinity\UiKernel\Theme\ActiveThemeContext;
use Symfinity\UiKernel\Theme\ThemePreferenceCookies;
use Symfinity\UiKernel\Theme\ThemePreferenceResolver;
use Symfinity\UiKernel\Theme\ThemeRegistry;
use Symfinity\UiProfiler\DataCollector\UiKernelDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** 078 — WDT semantic colour list from graph port. */
final class GraphVariantPanelTest extends TestCase
{
    #[Test]
    public function collectorPayloadIncludesGraphSlugs(): void
    {
        $port = new class implements GraphVariantCatalogPort {
            public function semanticColorSlugs(): array
            {
                return ['primary', 'danger', 'accent'];
            }

            public function layerSignature(): string
            {
                return 'panel-test-signature';
            }
        };

        $themeRegistry = new ThemeRegistry();
        $resolver = new ThemePreferenceResolver($themeRegistry, 'semantic');
        $activeThemeContext = new ActiveThemeContext(new ThemePreferenceCookies(), $resolver);
        $router = $this->createMock(UrlGeneratorInterface::class);
        $router->method('generate')->willThrowException(new RouteNotFoundException());

        $collector = new UiKernelDataCollector(
            $activeThemeContext,
            $resolver,
            $themeRegistry,
            $router,
            $port,
        );

        $collector->collect(Request::create('/demo'), new Response());

        self::assertSame(['primary', 'danger', 'accent'], $collector->getSemanticColorSlugs());
        self::assertSame('panel-test-signature', $collector->getGraphLayerSignature());
    }

    #[Test]
    public function templateDoesNotHardcodeEnumTable(): void
    {
        $template = (string) file_get_contents(dirname(__DIR__, 2) . '/templates/Collector/ui_kernel.html.twig');

        self::assertStringContainsString('collector.semanticColorSlugs', $template);
        self::assertStringNotContainsString('SemanticVariant', $template);
    }
}
