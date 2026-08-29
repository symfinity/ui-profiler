<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\TestCase;

/** symfinity 122 — collector panel h2 large variant: margin-top 0 + ui-kernel typography */
final class CollectorPanelHeadingTest extends TestCase
{
    public function testUiKernelPanelUsesCollectorPanelHeadingPartial(): void
    {
        $template = (string) file_get_contents(
            \dirname(__DIR__, 2).'/templates/Collector/ui_kernel.html.twig',
        );

        self::assertStringContainsString(
            'collector-panel-heading.html.twig',
            $template,
            'UI Kernel panel must use collector-panel-heading partial',
        );
        self::assertDoesNotMatchRegularExpression(
            '/<h2(?![^>]*data-ui-part)/',
            $template,
            'UI Kernel panel must not use bare h2',
        );
    }

    public function testTwigPanelUsesSinglePageHeadingAndSubheadingsForSections(): void
    {
        $template = (string) file_get_contents(
            \dirname(__DIR__, 2).'/templates/chrome/WebProfilerBundle/Collector/twig.html.twig',
        );

        self::assertSame(
            2,
            substr_count($template, 'collector-panel-heading.html.twig'),
            'Twig panel keeps one h2 per branch (empty vs populated)',
        );
        self::assertSame(
            3,
            substr_count($template, 'collector-panel-subheading.html.twig'),
            'Twig subsections must use collector-panel-subheading (h3)',
        );
        self::assertStringNotContainsString(
            "collector-panel-heading.html.twig', { title: 'Twig Metrics' })",
            $template,
        );
        self::assertStringNotContainsString(
            "collector-panel-heading.html.twig', { title: 'Rendered Templates' })",
            $template,
        );
    }

    public function testCollectorPanelSubheadingPartialRendersH3(): void
    {
        $partial = (string) file_get_contents(
            \dirname(__DIR__, 2).'/templates/_chrome/partials/collector-panel-subheading.html.twig',
        );

        self::assertStringContainsString('<h3', $partial);
        self::assertStringNotContainsString('<h2', $partial);
    }

    public function testShellCssPanelHeadingsUseStock24pxAndZeroTopMargin(): void
    {
        $shellCss = (string) file_get_contents(
            \dirname(__DIR__, 2).'/assets/styles/shell-chrome.css',
        );

        self::assertStringContainsString('font-size: 1.5rem', $shellCss);
        self::assertStringContainsString('margin-block: 0 1.875rem', $shellCss);
        self::assertMatchesRegularExpression(
            '/\[data-ui-profiler-collector-content\] > h2[\s\S]*margin-block: 0 1\.875rem/',
            $shellCss,
        );
        self::assertMatchesRegularExpression(
            '/\[data-ui-profiler-collector-content\] \[data-ui-role=section-heading\]\[data-ui-variant=panel\] \[data-ui-part=title\][\s\S]*font-size: 1\.5rem/',
            $shellCss,
        );
        self::assertStringNotContainsString('box-shadow: inset 0 -1px', $shellCss);
    }
}
