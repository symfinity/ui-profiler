<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfinity\UiProfiler\Css\ProfilerCssProvider;

/** symfinity 122 T017 — WDT CSS stays scoped to profiler hooks */
final class WdtSelectorScopeTest extends TestCase
{
  public function testWdtToolbarCssScopesCollectorBlocksUnderProfilerHook(): void
  {
    $css = ProfilerCssProvider::readFile(\dirname(__DIR__, 2), 'assets/styles/wdt-toolbar.css');

    self::assertMatchesRegularExpression('/\[data-ui-profiler-wdt\] \[data-ui-profiler-wdt-item\] \{/', $css);
    self::assertDoesNotMatchRegularExpression('/^\.sf-toolbar-block \{/m', $css);
    self::assertStringContainsString('[data-ui-profiler-wdt-root]', $css);
    self::assertStringNotContainsString('div.sf-toolbar .sf-toolbar-block', $css);
  }

    public function testWdtToolbarCssUsesKernelTokensAndCompositionHooks(): void
    {
        $css = ProfilerCssProvider::readFile(\dirname(__DIR__, 2), 'assets/styles/wdt-toolbar.css');

        self::assertStringContainsString('var(--ui-color-surface)', $css);
        self::assertStringContainsString('[data-ui-profiler-wdt-strip]', $css);
        self::assertStringContainsString('[data-ui-profiler-wdt-panel]', $css);
        self::assertStringContainsString('[data-ui-profiler-wdt-root][data-ui-profiler-wdt-state=closed]', $css);
        self::assertStringContainsString('[data-ui-profiler-wdt-toggle]', $css);
        self::assertStringNotContainsString('--ui-toolbar-bg:', $css);
    }
}
