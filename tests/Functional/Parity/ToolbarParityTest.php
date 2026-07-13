<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Functional\Parity;

use PHPUnit\Framework\Attributes\Group;
use Symfinity\UiKernel\Theme\ThemeLineageCatalog;
use Symfinity\UiKernel\Theme\ThemePreferenceCookies;
use Symfinity\UiProfiler\Tests\Fixtures\ParityKernel\ParityKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

#[Group('ui-profiler-parity')]
final class ToolbarParityTest extends WebTestCase
{
    use ParityBrowserTrait;

    protected static function getKernelClass(): string
    {
        return ParityKernel::class;
    }

    public function testToolbarContainsCollectorBlocksOnHomepage(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        $content = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('data-ui-profiler-wdt', $content);
        self::assertStringContainsString('data-ui-profiler-collector="request"', $content);
        self::assertStringContainsString('_profiler', $content);
    }

    public function testWdtHydrateRendersCollectorChipContent(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');

        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_wdt/' . $token);
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertMatchesRegularExpression(
            '/data-ui-profiler-collector="request".*?data-ui-profiler-wdt-chip.*?>\s*200/s',
            preg_replace('/\s+/', ' ', $content) ?? $content,
            'Request WDT chip must render HTTP status — twig:Link must not swallow captured icon blocks.',
        );
        self::assertStringContainsString('data-ui-profiler-wdt-value', $content);
        self::assertStringContainsString('aria-label="Request"', $content);
        self::assertStringNotContainsString('data-ui-profiler-wdt-collector-name', $content);
        self::assertStringNotContainsString('>Request<', preg_replace('/\s+/', ' ', $content) ?? $content);
        self::assertStringContainsString('data-ui-profiler-wdt-panel', $content);
        self::assertStringContainsString('HTTP status', $content);
    }

    public function testWdtToggleUsesSymfonyCloseAndSymfonyIcons(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');

        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_wdt/' . $token);
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('data-ui-profiler-wdt-toggle-icon="open"', $content);
        self::assertStringContainsString('data-ui-profiler-wdt-toggle-icon="closed"', $content);
        self::assertStringContainsString('aria-label="Symfony"', $content);
        self::assertStringContainsString('icon-tabler-x', $content);
        self::assertStringNotContainsString('data-ui-profiler-wdt-toggle-icon="open"></i>', $content);
    }

    public function testWdtStylesDockFlushAndAllowFlyoutOverflow(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/_wdt/styles');

        self::assertResponseIsSuccessful();

        $styles = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('[data-ui-profiler-wdt-strip]', $styles);
        self::assertMatchesRegularExpression('/\[data-ui-profiler-wdt-strip\][^{]*\{[^}]*overflow:\s*visible/', $styles);
        self::assertStringNotContainsString('[data-ui-profiler-wdt-collector-name]', $styles);
        self::assertStringContainsString('width: max-content', $styles);
        self::assertStringContainsString('bottom:0', preg_replace('/\s+/', '', $styles) ?? $styles);
    }

    public function testHostileGlobalCssDoesNotBreakToolbarLinks(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/parity/hostile-css');

        self::assertResponseIsSuccessful();
        $content = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('ui-profiler-wdt__trigger', $content);
        self::assertStringContainsString('data-ui-profiler-wdt', $content);
    }

    public function testToolbarKeepsInvariantDarkThemeWhenShellSchemeIsLight(): void
    {
        $darkThemeId = ThemeLineageCatalog::pairForLineage('default')['dark'];

        $client = static::createClient(['debug' => true]);
        $client->getCookieJar()->set(new Cookie(ThemePreferenceCookies::SCHEME, 'light'));
        $client->getCookieJar()->set(new Cookie(ThemePreferenceCookies::LINEAGE, 'default'));
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        $content = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('data-ui-profiler-wdt', $content);
        self::assertStringContainsString(sprintf('data-theme="%s"', $darkThemeId), $content);
    }

    public function testWdtStylesUseInvariantDarkToolbarBackground(): void
    {
        $darkThemeId = ThemeLineageCatalog::pairForLineage('default')['dark'];

        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/_wdt/styles');

        self::assertResponseIsSuccessful();

        $styles = (string) $client->getResponse()->getContent();
        self::assertStringContainsString(sprintf('[data-theme="%s"]', $darkThemeId), $styles);
        self::assertStringContainsString('var(--ui-color-surface)', $styles);
        self::assertStringContainsString('[data-ui-profiler-wdt-strip]', $styles);
        self::assertStringContainsString('[data-ui-profiler-wdt-panel]', $styles);
        self::assertMatchesRegularExpression('/\[data-ui-profiler-wdt-panel\][^{]*\{[^}]*border:\s*0/', $styles);
        self::assertStringContainsString('font-style: normal', $styles);
        self::assertStringContainsString('[data-ui-profiler-wdt-root][data-ui-profiler-wdt-state=closed]', $styles);
        self::assertMatchesRegularExpression(
            '/\[data-ui-profiler-wdt-root\]\[data-ui-profiler-wdt-state=closed\][^{]*\[data-ui-profiler-wdt-toggle\][^{]*\{[^}]*box-shadow:\s*none/',
            $styles,
        );
        self::assertStringNotContainsString('#262626', $styles);
        self::assertStringContainsString('[data-ui-profiler-wdt].ui-profiler-wdt {', $styles);
        self::assertStringContainsString('border: 0', $styles);
        self::assertStringContainsString('border-radius: 0', $styles);
        self::assertStringContainsString('max-width: calc(100vw - 1rem)', $styles);
        self::assertStringContainsString('width: max-content', $styles);
        self::assertStringContainsString('min-width: 0', $styles);
        self::assertStringNotContainsString('min(20rem', $styles);
        self::assertStringContainsString('--ui-profiler-wdt-surface:', $styles);
        self::assertStringContainsString('--ui-profiler-wdt-surface-hover:', $styles);
        self::assertStringContainsString('var(--ui-profiler-wdt-surface-hover)', $styles);
        self::assertStringContainsString('color: var(--ui-color-text-muted)', $styles);
        self::assertStringNotContainsString('var(--ui-color-primary) 12%, var(--ui-color-surface)', $styles);
        self::assertMatchesRegularExpression(
            '/\[data-ui-profiler-wdt\]\.ui-profiler-wdt[^{]*\{[^}]*box-shadow:\s*none/',
            $styles,
        );
        self::assertMatchesRegularExpression(
            '/\[data-ui-profiler-wdt-panel\][^{]*\{[^}]*box-shadow:\s*none/',
            $styles,
        );
        self::assertStringNotContainsString('--ui-profiler-shadow-wdt:', $styles);
        self::assertStringNotContainsString('color-mix(in srgb, var(--ui-color-text) 12%, transparent)', $styles);
    }

    public function testToolbarBackgroundStaysDarkWhenShellSchemeIsLight(): void
    {
        $darkThemeId = ThemeLineageCatalog::pairForLineage('default')['dark'];

        $client = static::createClient(['debug' => true]);
        $client->getCookieJar()->set(new Cookie(ThemePreferenceCookies::SCHEME, 'light'));
        $client->getCookieJar()->set(new Cookie(ThemePreferenceCookies::LINEAGE, 'default'));
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        $content = (string) $client->getResponse()->getContent();

        self::assertStringContainsString(sprintf('data-theme="%s"', $darkThemeId), $content);
        $normalized = preg_replace('/\s+/', '', $content) ?? $content;
        self::assertStringContainsString('var(--ui-color-surface)', $normalized);
    }
}
