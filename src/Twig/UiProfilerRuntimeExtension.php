<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Twig;

use Symfinity\UiKernel\Css\CssGenerator;
use Symfinity\UiKernel\Theme\ThemeLineageCatalog;
use Symfinity\UiKernel\Theme\ThemePreference;
use Symfinity\UiKernel\Theme\ThemeRegistry;
use Symfinity\UiProfiler\Asset\WdtToolbarStylesheet;
use Symfinity\UiProfiler\Menu\ProfilerMenuRenderer;
use Symfinity\UiProfiler\Theme\ThemeContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Greenfield 051 — theme, menu, WDT inline bootstrap; no owned .css files (ZOC-*).
 */
final class UiProfilerRuntimeExtension extends AbstractExtension
{
    private const WDT_SKELETON_CSS = <<<'CSS'
[data-ui-profiler-wdt].sf-toolbarreset{position:fixed;left:0;right:0;bottom:0;z-index:99999;min-height:36px;margin:0;padding:0 36px 0 0;background:var(--ui-color-surface);color:var(--ui-color-text);font:11px var(--ui-font-family-sans,system-ui,sans-serif);text-align:left;box-shadow:inset 0 1px 0 color-mix(in srgb,var(--ui-color-text) 12%,var(--ui-color-surface)),0 -1px 0 color-mix(in srgb,var(--ui-color-text) 25%,transparent);user-select:none}
.sf-toolbar-clearer{clear:both;height:36px}
.sf-toolbar-block{display:block;float:left;height:36px;max-width:25%;white-space:nowrap}
.sf-toolbar-block>a{display:block;text-decoration:none;color:inherit}
.sf-toolbar-block .sf-toolbar-icon{display:flex;align-items:center;height:36px;padding:0 7px;color:var(--ui-color-text-muted)}
.sf-toolbar-block .sf-toolbar-value{color:var(--ui-color-text);font-size:13px;line-height:36px}
.sf-toolbarreset svg{height:20px;width:20px;display:inline-block}
.sf-toolbar-block .sf-toolbar-icon svg+span{margin-left:4px}
.sf-toolbar-info{display:none}
.sf-toolbarreset .sf-toolbar-toggle-button{position:absolute;top:1px;right:0;width:36px;height:35px;border:0;background:var(--ui-color-surface);color:var(--ui-color-text-muted);cursor:pointer;padding:0;margin:0}
.sf-toolbar-toggle-button i{display:grid;height:35px;place-content:center;width:100%}
.sf-toolbar-opened .sf-toolbar-toggle-button .sf-toolbar-icon-closed{display:none}
.sf-toolbar-opened .sf-toolbar-toggle-button .sf-toolbar-icon-opened::before{content:"\00d7";display:block;font-size:20px;line-height:1}
.sf-toolbar-closed .sf-toolbar-clearer{display:none}
.sf-toolbar-closed .sf-toolbarreset{height:0;min-height:0;background:0 0;box-shadow:none;padding:0;overflow:visible}
.sf-toolbar-closed .sf-toolbar-block{display:none}
.sf-toolbar-closed .sf-toolbar-toggle-button{top:-37px;border-top:2px solid var(--ui-color-surface)}
.sf-toolbar-closed .sf-toolbar-toggle-button .sf-toolbar-icon-opened{display:none}
CSS;

    public function __construct(
        private readonly ThemeContext $themeContext,
        private readonly CssGenerator $cssGenerator,
        private readonly ThemeRegistry $themeRegistry,
        private readonly ProfilerMenuRenderer $menuRenderer,
        private readonly bool $wdtCriticalInline,
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
            new TwigFunction('ui_profiler_is_dark_theme', $this->isDarkTheme(...)),
            new TwigFunction('ui_profiler_wdt_critical_inline', $this->wdtCriticalInline(...)),
            new TwigFunction('ui_profiler_wdt_critical_css_inline', $this->wdtCriticalInline(...)),
            new TwigFunction('ui_profiler_wdt_critical_css', $this->wdtCriticalCss(...)),
            new TwigFunction('ui_profiler_wdt_stylesheet', $this->wdtStylesheet(...)),
            new TwigFunction('ui_profiler_wdt_theme_tokens_css', $this->wdtThemeTokensCss(...)),
            new TwigFunction('ui_profiler_asset_version', $this->assetVersion(...)),
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

    public function isDarkTheme(): bool
    {
        return ThemeLineageCatalog::isDarkThemeId($this->themeContext->resolveShellThemeId());
    }

    public function wdtCriticalInline(): bool
    {
        return $this->wdtCriticalInline;
    }

    public function wdtCriticalCss(): string
    {
        if (!$this->wdtCriticalInline) {
            return '';
        }

        $css = $this->wdtCriticalThemeTokensCss()."\n".self::WDT_SKELETON_CSS;

        return preg_replace('/\s+/', ' ', trim($css)) ?? trim($css);
    }

    public function wdtThemeTokensCss(): string
    {
        return $this->cssGenerator->forTheme(
            $this->themeRegistry->resolve($this->themeContext->resolveToolbarThemeId()),
        );
    }

    public function wdtStylesheet(): string
    {
        return WdtToolbarStylesheet::CSS;
    }

    public function assetVersion(): string
    {
        return '0';
    }

    public function stimulusEnabled(): bool
    {
        return $this->stimulusEnabled;
    }

    private function wdtCriticalThemeTokensCss(): string
    {
        $themeId = $this->themeContext->resolveToolbarThemeId();
        $tokens = $this->themeRegistry->resolve($themeId)->tokens()->all();
        $keys = [
            '--ui-color-surface',
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
}
