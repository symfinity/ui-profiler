<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfinity\UiProfiler\Asset\ShellChromeStylesheet;

final class ShellLineBudgetTest extends TestCase
{
    private const FIXTURE_PATH = __DIR__.'/../Fixtures/shell-line-budget-baseline.json';

    public function testCurrentCssBodyDoesNotExceedWaveZeroBaseline(): void
    {
        /** @var array{css_body_lines: int} $baseline */
        $baseline = $this->loadFixture();
        $actual = self::countCssBodyNonEmptyLines(ShellChromeStylesheet::css());

        self::assertLessThanOrEqual(
            $baseline['css_body_lines'],
            $actual,
            'CSS-body grew above wave-0 baseline — update baseline only after intentional scope expansion.',
        );
    }

    public function testShellCssBodyWithinActiveWaveCeiling(): void
    {
        /** @var array{
         *     css_body_lines: int,
         *     active_wave: int,
         *     wave_ceilings: array<string, int>
         * } $baseline
         */
        $baseline = $this->loadFixture();
        $activeWave = (string) $baseline['active_wave'];
        self::assertArrayHasKey($activeWave, $baseline['wave_ceilings']);

        $percent = $baseline['wave_ceilings'][$activeWave];
        $ceiling = (int) floor($baseline['css_body_lines'] * $percent / 100);
        $actual = self::countCssBodyNonEmptyLines(ShellChromeStylesheet::css());

        self::assertLessThanOrEqual(
            $ceiling,
            $actual,
            sprintf(
                'Shell CSS-body lines %d exceed wave %s ceiling %d (%d%% of baseline %d). Delegate to ux-blocks-core or update active_wave after wave merge.',
                $actual,
                $activeWave,
                $ceiling,
                $percent,
                $baseline['css_body_lines'],
            ),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function loadFixture(): array
    {
        $decoded = json_decode((string) file_get_contents(self::FIXTURE_PATH), true, 512, \JSON_THROW_ON_ERROR);
        self::assertIsArray($decoded);

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    private static function countCssBodyNonEmptyLines(string $css): int
    {
        $css = preg_replace('#/\*.*?\*/#s', '', $css) ?? $css;

        return substr_count($css, '{');
    }
}
