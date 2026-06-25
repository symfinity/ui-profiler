<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfinity\UiProfiler\Asset\ShellChromeStylesheet;

/**
 * T036 — embedded shell CSS MUST target profiler hooks, not legacy layout IDs.
 *
 * @see specs/symfinity/symfinity/122-ui-profiler-shell-delegation/contracts/shell-delegation-boundary.md
 */
final class ShellSelectorScopeTest extends TestCase
{
    /** Legacy layout IDs replaced by data-ui-profiler-* hooks (wave-8 cleanup). */
    private const FORBIDDEN_LEGACY_IDS = [
        '#summary',
        '#sidebar',
        '#header',
        '#collector-content',
        '#collector-wrapper',
        '#menu-profiler',
        '#main',
    ];

    public function testShellCssDoesNotTargetForbiddenLegacyLayoutIds(): void
    {
        $css = ShellChromeStylesheet::CSS;

        foreach (self::FORBIDDEN_LEGACY_IDS as $legacyId) {
            self::assertStringNotContainsString(
                $legacyId,
                $css,
                sprintf('Shell CSS still targets legacy id %s — use a data-ui-profiler-* hook.', $legacyId),
            );
        }
    }

    public function testShellCssUsesProfilerHookSelectors(): void
    {
        $css = ShellChromeStylesheet::CSS;

        foreach ([
            '[data-ui-profiler-shell]',
            '[data-ui-profiler-request-summary]',
            '[data-ui-profiler-sidebar]',
            '[data-ui-profiler-collector-content]',
            '[data-ui-profiler-header]',
            '[data-ui-profiler-menu]',
        ] as $hook) {
            self::assertStringContainsString($hook, $css, sprintf('Expected shell CSS to include hook %s.', $hook));
        }
    }
}
