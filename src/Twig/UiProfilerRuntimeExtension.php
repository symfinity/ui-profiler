<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Twig;

use Symfinity\UiKernel\Css\CssGenerator;
use Symfinity\UiKernel\Theme\ThemeLineageCatalog;
use Symfinity\UiKernel\Theme\ThemePreference;
use Symfinity\UiKernel\Theme\ThemeRegistry;
use Symfinity\UiProfiler\Asset\ShellChromeStylesheet;
use Symfinity\UiProfiler\Asset\WdtCriticalCss;
use Symfinity\UiProfiler\Asset\WdtToolbarStylesheet;
use Symfinity\UiProfiler\Menu\ProfilerMenuRenderer;
use Symfinity\UiProfiler\Theme\ThemeContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class UiProfilerRuntimeExtension extends AbstractExtension
{
    public function __construct(
        private readonly ThemeContext $themeContext,
        private readonly CssGenerator $cssGenerator,
        private readonly ThemeRegistry $themeRegistry,
        private readonly ProfilerMenuRenderer $menuRenderer,
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
            new TwigFunction('ui_profiler_asset_version', $this->resolveAssetVersion(...)),
            new TwigFunction('ui_profiler_is_dark_theme', $this->isDarkTheme(...)),
            new TwigFunction('ui_profiler_wdt_stylesheet', $this->wdtStylesheet(...)),
            new TwigFunction('ui_profiler_wdt_theme_tokens_css', $this->wdtThemeTokensCss(...)),
            new TwigFunction('ui_profiler_shell_stylesheet', $this->shellStylesheet(...)),
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
        return WdtToolbarStylesheet::CSS;
    }

    public function shellStylesheet(): string
    {
        return ShellChromeStylesheet::CSS;
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

        $css = $this->wdtCriticalThemeTokensCss()."\n".WdtCriticalCss::CSS;

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
