<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Functional\Parity;

use PHPUnit\Framework\Attributes\Group;
use Symfinity\UiProfiler\Tests\ParityKernel\ParityKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[Group('ui-profiler-parity')]
final class WdtBootstrapParityTest extends WebTestCase
{
    private const MAX_BOOTSTRAP_BYTES = 5120;

    protected static function getKernelClass(): string
    {
        return ParityKernel::class;
    }

    public function testToolbarBootstrapHasNoStockSfjsAndStaysSmall(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        $content = (string) $client->getResponse()->getContent();

        self::assertStringNotContainsString('Sfjs = (function', $content);
        self::assertStringNotContainsString('typeof Sfjs === \'undefined\'', $content);
        self::assertStringContainsString('UiProfilerWdt.boot', $content);
        self::assertStringContainsString('/_profiler/assets/js/wdt.js', $content);

        $bootstrap = $this->extractToolbarBootstrap($content);
        self::assertMatchesRegularExpression(
            '/position\s*:\s*fixed/',
            $bootstrap,
            'Critical CSS must style skeleton before toolbar.css loads.',
        );
        self::assertLessThanOrEqual(
            self::MAX_BOOTSTRAP_BYTES,
            strlen($bootstrap),
            'WDT inline bootstrap must stay ≤ 5KB (V2-02).',
        );
    }

    public function testOwnedWdtScriptRouteReturnsJavaScript(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/_profiler/assets/js/wdt.js?v=0');

        self::assertResponseIsSuccessful();
        self::assertSame('text/javascript; charset=UTF-8', $client->getResponse()->headers->get('Content-Type'));
        $script = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('UiProfilerWdt', $script);
        self::assertStringContainsString('positionWdtPanel', $script);
        self::assertStringContainsString('getBoundingClientRect', $script);
    }

    public function testToolbarUsesOwnedStylesheetRoute(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        $content = (string) $client->getResponse()->getContent();

        self::assertStringNotContainsString('ui-profiler/wdt/toolbar.css', $content);
        self::assertStringContainsString('/_wdt/styles', $content);
        self::assertMatchesRegularExpression('#/_wdt/styles\?v=\d+#', $content);
    }

    private function extractToolbarBootstrap(string $html): string
    {
        $start = strpos($html, '<!-- START of Symfony Web Debug Toolbar -->');
        $end = strpos($html, '<!-- END of Symfony Web Debug Toolbar -->');

        if (false === $start || false === $end || $end <= $start) {
            self::fail('WDT bootstrap markers missing from HTML response.');
        }

        return substr($html, $start, $end - $start + strlen('<!-- END of Symfony Web Debug Toolbar -->'));
    }
}
