<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * 148 zero-legacy CSS gate — owned CSS MUST NOT ship forbidden Symfony WebProfiler shell vocabulary.
 *
 * @see specs/symfinity/symfinity/148-ui-profiler-zero-legacy-css/contracts/zero-legacy-css-gate.md
 * @see specs/symfinity/symfinity/148-ui-profiler-zero-legacy-css/contracts/sf-dump-compat-boundary.md
 */
#[Group('ui-profiler-parity')]
final class LegacyCssSelectorTest extends TestCase
{
    private const BASELINE_PATH = __DIR__.'/../baselines/css-budget-baseline.json';

    /** Forbidden shell/WDT vocabulary — MUST NOT appear in owned CSS (148 gate). */
    private const FORBIDDEN_PATTERNS = [
        '\.sf-toolbar\b',
        '\.sf-toolbar-block\b',
        '\.sf-toolbar-clearer\b',
        'div\.sf-toolbar\b',
        '\.sf-tabs\b',
        '\.sf-toggle\b',
        '\.sf-toggle-visible\b',
        '\.sf-reset\b',
        '\.sf-profiler-timeline\b',
        '\.sf-serializer\b',
        '\.sf-validator\b',
        '#summary\b',
        '#sidebar\b',
        '#header\b',
        '#collector-content\b',
        '#menu-profiler\b',
        '--sf-[a-z0-9-]+',
    ];

    /** VarDumper compat — permitted only under profiler collector/panel hooks. */
    private const SF_DUMP_PATTERN = '/(?:^|[\s,>+~]|:)pre\.sf-dump\b|\.sf-dump[\w-]*/';

    private const COMPAT_ANCESTOR_HOOKS = [
        '[data-ui-profiler-collector-content]',
        '[data-ui-profiler-panel]',
        '[data-ui-profiler-wdt-panel]',
    ];

    public function testAllowlistedCssFilesHaveNoForbiddenLegacySelectors(): void
    {
        /** @var array{files: array<string, int>} $baseline */
        $baseline = json_decode((string) file_get_contents(self::BASELINE_PATH), true, 512, \JSON_THROW_ON_ERROR);
        $packageDir = \dirname(__DIR__, 2);
        $violations = [];

        foreach (array_keys($baseline['files']) as $relativePath) {
            $absolutePath = $packageDir.'/'.$relativePath;
            self::assertFileExists($absolutePath, $relativePath);
            $violations = array_merge(
                $violations,
                self::auditCssFile($relativePath, (string) file_get_contents($absolutePath)),
            );
        }

        self::assertSame([], $violations);
    }

    public function testSfDumpSelectorsAreScopedToCompatAncestors(): void
    {
        /** @var array{files: array<string, int>} $baseline */
        $baseline = json_decode((string) file_get_contents(self::BASELINE_PATH), true, 512, \JSON_THROW_ON_ERROR);
        $packageDir = \dirname(__DIR__, 2);
        $violations = [];

        foreach (array_keys($baseline['files']) as $relativePath) {
            $absolutePath = $packageDir.'/'.$relativePath;
            $violations = array_merge(
                $violations,
                self::auditSfDumpCompat($relativePath, (string) file_get_contents($absolutePath)),
            );
        }

        self::assertSame([], $violations);
    }

    /**
     * @return list<string>
     */
    private static function auditCssFile(string $relativePath, string $css): array
    {
        $violations = [];
        $normalized = preg_replace('#/\*.*?\*/#s', '', $css) ?? $css;

        foreach (self::FORBIDDEN_PATTERNS as $pattern) {
            if (preg_match_all('/'.$pattern.'/i', $normalized, $matches, \PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as [$match]) {
                    $violations[] = sprintf('%s: forbidden legacy selector %s', $relativePath, $match);
                }
            }
        }

        return $violations;
    }

    /**
     * @return list<string>
     */
    private static function auditSfDumpCompat(string $relativePath, string $css): array
    {
        $violations = [];
        $blocks = preg_split('/\}/', $css) ?: [];

        foreach ($blocks as $block) {
            if (!preg_match(self::SF_DUMP_PATTERN, $block)) {
                continue;
            }

            $selectorPart = explode('{', $block, 2)[0];
            $hasCompatAncestor = false;
            foreach (self::COMPAT_ANCESTOR_HOOKS as $hook) {
                if (str_contains($selectorPart, $hook)) {
                    $hasCompatAncestor = true;
                    break;
                }
            }

            if (!$hasCompatAncestor) {
                $snippet = trim(preg_replace('/\s+/', ' ', $selectorPart) ?? $selectorPart);
                $violations[] = sprintf(
                    '%s: .sf-dump compat selector lacks profiler hook ancestor: %s',
                    $relativePath,
                    mb_substr($snippet, 0, 120),
                );
            }
        }

        return $violations;
    }
}
