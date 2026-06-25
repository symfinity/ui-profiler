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
        self::assertStringContainsString('font-style: normal', $styles);
        self::assertStringContainsString('[data-ui-profiler-wdt-root][data-ui-profiler-wdt-state=closed]', $styles);
        self::assertStringNotContainsString('#262626', $styles);
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
