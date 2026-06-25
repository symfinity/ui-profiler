<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Twig;

use Symfinity\UiKernel\Css\CssGenerator;
use Symfinity\UiKernel\Theme\ActiveThemeContext;
use Symfinity\UiKernel\Theme\ThemeColorScheme;
use Symfinity\UiKernel\Theme\ThemeLineageCatalog;
use Symfinity\UiKernel\Theme\ThemePreference;
use Symfinity\UiKernel\Theme\ThemeRegistry;
use Symfinity\UiProfiler\Css\ProfilerCssProvider;
use Symfinity\UiProfiler\Menu\ProfilerMenuRenderer;
use Symfinity\UiProfiler\Theme\ThemeContext;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class UiProfilerRuntimeExtension extends AbstractExtension
{
    public function __construct(
        private readonly ThemeContext $themeContext,
        private readonly ActiveThemeContext $activeThemeContext,
        private readonly RequestStack $requestStack,
        private readonly Environment $twig,
        private readonly CssGenerator $cssGenerator,
        private readonly ThemeRegistry $themeRegistry,
        private readonly ProfilerMenuRenderer $menuRenderer,
        private readonly ProfilerCssProvider $cssProvider,
        private readonly string $bundlePath,
        private readonly bool $wdtCriticalCssInline,
        private readonly bool $wdtModulePreload,
        private readonly bool $legacySfShim,
        private readonly ?string $assetVersion,
        private readonly bool $uiProfilerEnabled,
        private readonly bool $stimulusEnabled,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ui_profiler_theme', $this->resolveThemeId(...)),
            new TwigFunction('ui_profiler_toolbar_theme', $this->resolveToolbarThemeId(...)),
            new TwigFunction('ui_profiler_theme_preference', $this->resolvePreference(...)),
            new TwigFunction('ui_profiler_lineage_pairs', $this->resolveLineagePairs(...)),
            new TwigFunction('ui_profiler_paint_guard_surfaces', $this->paintGuardSurfaces(...)),
            new TwigFunction('ui_profiler_asset_version', $this->resolveAssetVersion(...)),
            new TwigFunction('ui_profiler_is_dark_theme', $this->isDarkTheme(...)),
            new TwigFunction('ui_profiler_wdt_stylesheet', $this->wdtStylesheet(...)),
            new TwigFunction('ui_profiler_wdt_theme_tokens_css', $this->wdtThemeTokensCss(...)),
            new TwigFunction('ui_profiler_shell_stylesheet', $this->shellStylesheet(...)),
            new TwigFunction('ui_profiler_theme_boot_script', $this->profilerThemeBootScript(...), ['is_safe' => ['html']]),
            new TwigFunction('ui_profiler_shell_theme_css', $this->profilerShellThemeCss(...), ['is_safe' => ['html']]),
            new TwigFunction('ui_profiler_wdt_critical_css_inline', $this->wdtCriticalCssInline(...)),
            new TwigFunction('ui_profiler_wdt_critical_css', $this->wdtCriticalCss(...)),
            new TwigFunction('ui_profiler_wdt_module_preload', $this->wdtModulePreload(...)),
            new TwigFunction('ui_profiler_legacy_sf_shim', $this->legacySfShim(...)),
            new TwigFunction('ui_profiler_legacy_shim_stylesheet', $this->legacyShimStylesheet(...)),
            new TwigFunction('ui_profiler_menu', $this->renderMenu(...), ['is_safe' => ['html']]),
            new TwigFunction('ui_profiler_stimulus_enabled', $this->stimulusEnabled(...)),
        ];
    }

    public function renderMenu(string $collectorId, object $collector): string
    {
        if (!$this->uiProfilerEnabled) {
            return '';
        }

        return $this->menuRenderer->render($collectorId, $collector);
    }

    public function stimulusEnabled(): bool
    {
        return $this->stimulusEnabled;
    }

    public function resolveThemeId(): string
    {
        return $this->themeContext->resolveShellThemeId();
    }

    public function resolveToolbarThemeId(): string
    {
        return $this->themeContext->resolveToolbarThemeId();
    }

    public function resolvePreference(): ThemePreference
    {
        return $this->themeContext->preference();
    }

    /**
     * @return array<string, array{light: string, dark: string}>
     */
    public function resolveLineagePairs(): array
    {
        return $this->themeContext->lineagePairs();
    }

    /**
     * @return array{light: string, dark: string}
     */
    public function paintGuardSurfaces(?string $lineage = null): array
    {
        return $this->themeContext->paintGuardSurfaces($lineage);
    }

    public function resolveAssetVersion(): string
    {
        return $this->assetVersion ?? '0';
    }

    public function isDarkTheme(): bool
    {
        return ThemeLineageCatalog::isDarkThemeId($this->themeContext->resolveShellThemeId());
    }

    public function wdtStylesheet(): string
    {
        return $this->cssProvider->wdtToolbarStylesheet();
    }

    public function shellStylesheet(): string
    {
        return $this->cssProvider->shellStylesheet();
    }

    public function profilerThemeBootScript(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return '';
        }

        $config = $this->activeThemeContext->bootConfigFromRequest($request);
        if ($config['scheme'] === ThemeColorScheme::Auto->value) {
            // Client resolves auto via prefers-color-scheme; skip async PATCH CSS swap (flash).
            $config['resolvedThemeId'] = null;
            // SSR resolvedColorScheme is light without Client Hints — must not override native dark chrome.
            $config['resolvedColorScheme'] = null;
        }

        return $this->twig->render('@UiKernel/_theme_boot_script.html.twig', [
            'boot_config' => $config,
        ]);
    }

    public function profilerShellThemeCss(): string
    {
        $preference = $this->themeContext->preference();
        if ($preference->scheme === ThemeColorScheme::Auto) {
            $pair = ThemeLineageCatalog::pairForLineage($preference->lineage);

            return $this->wrapThemeCss(
                $this->cssGenerator->forTheme($this->themeRegistry->resolve($pair['light']))
                ."\n"
                .$this->cssGenerator->forTheme($this->themeRegistry->resolve($pair['dark'])),
            );
        }

        return $this->wrapThemeCss(
            $this->cssGenerator->forTheme(
                $this->themeRegistry->resolve($this->themeContext->resolveShellThemeId()),
            ),
        );
    }

    private function wrapThemeCss(string $css): string
    {
        return sprintf("<style id=\"ui-kernel-theme-css\">\n%s\n</style>", trim($css));
    }

    /**
     * Twig compiles ui_profiler_wdt_critical_css_inline to this method name in cached templates.
     */
    public function wdtCriticalInline(): bool
    {
        return $this->wdtCriticalCssInline;
    }

    public function wdtCriticalCssInline(): bool
    {
        return $this->wdtCriticalCssInline;
    }

    public function wdtThemeTokensCss(): string
    {
        return $this->cssGenerator->forTheme(
            $this->themeRegistry->resolve($this->themeContext->resolveToolbarThemeId()),
        );
    }

    public function wdtCriticalCss(): string
    {
        if (!$this->wdtCriticalCssInline) {
            return '';
        }

        $css = $this->wdtCriticalThemeTokensCss()."\n".$this->cssProvider->wdtCriticalCss();

        return preg_replace('/\s+/', ' ', trim($css)) ?? trim($css);
    }

    private function wdtCriticalThemeTokensCss(): string
    {
        $themeId = $this->themeContext->resolveToolbarThemeId();
        $tokens = $this->themeRegistry->resolve($themeId)->tokens()->all();
        $keys = [
            '--ui-color-surface',
            '--ui-color-surface-elevated',
            '--ui-color-text',
            '--ui-color-text-muted',
            '--ui-font-family-sans',
        ];

        $lines = [sprintf('[data-theme="%s"] {', $themeId)];
        foreach ($keys as $key) {
            if (isset($tokens[$key])) {
                $lines[] = sprintf('  %s: %s;', $key, $tokens[$key]);
            }
        }
        $lines[] = '}';

        return implode("\n", $lines);
    }

    public function wdtModulePreload(): bool
    {
        return $this->wdtModulePreload;
    }

    public function legacySfShim(): bool
    {
        return $this->legacySfShim;
    }

    public function legacyShimStylesheet(): string
    {
        return '';
    }
}
