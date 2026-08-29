<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit\Theme;

use PHPUnit\Framework\TestCase;
use Symfinity\UiKernel\Theme\ActiveThemeContext;
use Symfinity\UiKernel\Theme\ThemeColorScheme;
use Symfinity\UiKernel\Theme\ThemeLineageCatalog;
use Symfinity\UiKernel\Theme\ThemePreferenceCookies;
use Symfinity\UiKernel\Theme\ThemePreferenceResolver;
use Symfinity\UiKernel\Theme\ThemeRegistry;
use Symfinity\UiProfiler\Theme\ThemeContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ThemeContextTest extends TestCase
{
    private ThemeRegistry $registry;

    private ThemePreferenceResolver $resolver;

    private ActiveThemeContext $activeThemeContext;

    protected function setUp(): void
    {
        $this->registry = new ThemeRegistry();
        $this->resolver = new ThemePreferenceResolver($this->registry, 'default');
        $this->activeThemeContext = new ActiveThemeContext(
            new ThemePreferenceCookies(),
            $this->resolver,
        );
    }

    public function testToolbarThemeIsDarkVariantWhenShellResolvesLight(): void
    {
        $darkId = ThemeLineageCatalog::pairForLineage('default')['dark'];
        $lightId = ThemeLineageCatalog::pairForLineage('default')['light'];

        $request = Request::create('/');
        $request->cookies->set(ThemePreferenceCookies::SCHEME, ThemeColorScheme::Light->value);
        $request->cookies->set(ThemePreferenceCookies::LINEAGE, 'default');

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $context = new ThemeContext($requestStack, $this->activeThemeContext, $this->resolver, $this->registry, null);

        self::assertSame($lightId, $context->resolveShellThemeId());
        self::assertSame($darkId, $context->resolveToolbarThemeId());
        self::assertTrue(ThemeLineageCatalog::isDarkThemeId($context->resolveToolbarThemeId()));
    }

    public function testToolbarThemeOverrideWhenConfigured(): void
    {
        $darkId = ThemeLineageCatalog::pairForLineage('default')['dark'];

        $request = Request::create('/');
        $request->cookies->set(ThemePreferenceCookies::SCHEME, ThemeColorScheme::Light->value);
        $request->cookies->set(ThemePreferenceCookies::LINEAGE, 'default');

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $context = new ThemeContext($requestStack, $this->activeThemeContext, $this->resolver, $this->registry, $darkId);

        self::assertSame($darkId, $context->resolveToolbarThemeId());
    }

    public function testToolbarThemeWithoutRequestUsesDefaultLineageDark(): void
    {
        $darkId = ThemeLineageCatalog::pairForLineage('default')['dark'];

        $context = new ThemeContext(new RequestStack(), $this->activeThemeContext, $this->resolver, $this->registry, null);

        self::assertSame($darkId, $context->resolveToolbarThemeId());
    }

    public function testPaintGuardSurfacesResolveKernelSurfaceTokens(): void
    {
        $pair = ThemeLineageCatalog::pairForLineage('default');
        $lightSurface = $this->registry->resolve($pair['light'])->tokens()->all()['--ui-color-surface'];
        $darkSurface = $this->registry->resolve($pair['dark'])->tokens()->all()['--ui-color-surface'];

        $context = new ThemeContext(new RequestStack(), $this->activeThemeContext, $this->resolver, $this->registry, null);

        self::assertSame(
            ['light' => $lightSurface, 'dark' => $darkSurface],
            $context->paintGuardSurfaces('default'),
        );
        self::assertNotSame($lightSurface, $darkSurface);
    }
}
