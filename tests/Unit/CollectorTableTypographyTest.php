<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\TestCase;

/** symfinity 122 — collector tables use uniform sm sans cell typography in shell SSOT */
final class CollectorTableTypographyTest extends TestCase
{
    public function testShellCssDeclaresUniformCollectorTableTypography(): void
    {
        $shellCss = (string) file_get_contents(
            \dirname(__DIR__, 2).'/assets/styles/shell-chrome.css',
        );

        self::assertMatchesRegularExpression(
            '/\[data-ui-profiler-collector-content\] \[data-ui-role=table\][\s\S]*font-size: var\(--ui-font-size-sm\)/',
            $shellCss,
        );
        self::assertMatchesRegularExpression(
            '/\[data-ui-profiler-collector-content\] \[data-ui-role=table\] :is\(thead th, tbody :is\(th, td\)\)[\s\S]*font-family: var\(--ui-font-family-sans\)/',
            $shellCss,
        );
        self::assertStringContainsString(
            ':is(th, td)[data-ui-role=typography]',
            $shellCss,
        );
    }

    public function testEventsTableMacroDoesNotUseTypographyRoleOnCells(): void
    {
        $template = (string) file_get_contents(
            \dirname(__DIR__, 2).'/templates/chrome/WebProfilerBundle/Collector/events.html.twig',
        );

        self::assertDoesNotMatchRegularExpression(
            '/render_table[\s\S]*data-ui-role="typography"/',
            $template,
            'Events table cells must not declare typography role (conflicts with table SSOT)',
        );
    }
}
