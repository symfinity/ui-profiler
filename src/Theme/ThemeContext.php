<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Theme;

use Symfinity\UiKernel\Theme\ActiveThemeContext;
use Symfinity\UiKernel\Theme\ThemeLineageCatalog;
use Symfinity\UiKernel\Theme\ThemePreference;
use Symfinity\UiKernel\Theme\ThemePreferenceResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Profiler theme facade — delegates persistence to ui-kernel cookies (KTB-3).
 *
 * Toolbar bar stays dark when toolbar.dark_bar is true (051 KTB-T*).
 */
final class ThemeContext
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ActiveThemeContext $activeThemeContext,
        private readonly ThemePreferenceResolver $preferenceResolver,
        private readonly bool $toolbarDarkBar,
    ) {
    }

    public function resolveShellThemeId(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return $this->preferenceResolver->resolveThemeId(
                ThemePreference::defaults($this->preferenceResolver->defaultLineage()),
                false,
            );
        }

        return $this->activeThemeContext->resolvedThemeIdFromRequest($request);
    }

    public function resolveToolbarThemeId(): string
    {
        if (!$this->toolbarDarkBar) {
            return $this->resolveShellThemeId();
        }

        return ThemeLineageCatalog::pairForLineage($this->preference()->lineage)['dark'];
    }

    public function preference(): ThemePreference
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return ThemePreference::defaults($this->preferenceResolver->defaultLineage());
        }

        return $this->activeThemeContext->preferenceFromRequest($request);
    }

    /**
     * @return array<string, array{light: string, dark: string}>
     */
    public function lineagePairs(): array
    {
        return ThemeLineageCatalog::pairs();
    }
}
