<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\DataCollector;

use Symfinity\UiKernel\Contract\Catalog\GraphVariantCatalogPort;
use Symfinity\UiKernel\Theme\ActiveThemeContext;
use Symfinity\UiKernel\Theme\Theme;
use Symfinity\UiKernel\Theme\ThemePreferenceResolver;
use Symfinity\UiKernel\Theme\ThemeRegistry;
use Symfinity\UiKernel\Token\ThemeTokenSchema;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class UiKernelDataCollector extends DataCollector
{
    public const CSS_BYTES_REQUEST_ATTR = '_symfinity_ui_kernel_css_bytes';

    /** @var list<string> */
    private const SHOWCASE_ROUTES = ['ux_blocks_demo_kernel'];

    public function __construct(
        private readonly ActiveThemeContext $activeThemeContext,
        private readonly ThemePreferenceResolver $resolver,
        private readonly ThemeRegistry $themeRegistry,
        private readonly UrlGeneratorInterface $router,
        private readonly GraphVariantCatalogPort $variantCatalog,
    ) {
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $path = $request->getPathInfo();
        if (str_starts_with($path, '/_wdt') || str_starts_with($path, '/_profiler')) {
            $this->data = ['enabled' => true];

            return;
        }

        $preference = $this->activeThemeContext->preferenceFromRequest($request);
        $systemPrefersDark = $this->resolver->resolveSystemPrefersDark($request);
        $themeId = $this->activeThemeContext->resolvedThemeIdFromRequest($request);
        $activeTheme = $this->activeThemeContext->activeThemeFromRequest($request);

        /** @var list<array{id: string, label: string}> $themes */
        $themes = [];
        foreach ($this->themeRegistry->all() as $theme) {
            $themes[] = [
                'id' => $theme->id(),
                'label' => $theme->label(),
            ];
        }

        usort(
            $themes,
            static fn (array $a, array $b): int => $a['id'] <=> $b['id'],
        );

        $this->data = [
            'enabled' => true,
            'themeId' => $themeId,
            'lineage' => $preference->lineage,
            'scheme' => $preference->scheme->value,
            'systemPrefersDark' => $systemPrefersDark,
            'cssBytes' => $this->resolveCssBytes($request),
            'tokenCount' => \count($activeTheme->tokens()->all()),
            'activeTheme' => [
                'id' => $activeTheme->id(),
                'label' => $activeTheme->label(),
                'schemaVersion' => $activeTheme->schemaVersion(),
                'scrollMotion' => $activeTheme->scrollMotion(),
            ],
            'themes' => $themes,
            'themeCount' => \count($themes),
            'colorPalette' => $this->buildColorPalette($activeTheme),
            'semantic_color_slugs' => $this->variantCatalog->semanticColorSlugs(),
            'graph_layer_signature' => $this->variantCatalog->layerSignature(),
            'showcaseUrl' => $this->resolveShowcaseUrl(),
        ];
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getName(): string
    {
        return 'ui_kernel';
    }

    public function isEnabled(): bool
    {
        return $this->dataBool('enabled');
    }

    public function getThemeId(): ?string
    {
        $themeId = $this->dataArray()['themeId'] ?? null;

        return is_string($themeId) ? $themeId : null;
    }

    public function getLineage(): string
    {
        return $this->dataString('lineage');
    }

    public function getScheme(): string
    {
        return $this->dataString('scheme');
    }

    public function isSystemPrefersDark(): bool
    {
        return $this->dataBool('systemPrefersDark');
    }

    public function getCssBytes(): int
    {
        return $this->dataInt('cssBytes');
    }

    public function getThemeCount(): int
    {
        return $this->dataInt('themeCount');
    }

    public function getTokenCount(): int
    {
        return $this->dataInt('tokenCount');
    }

    /**
     * @return array{id: string, label: string, schemaVersion: string, scrollMotion: bool}
     */
    public function getActiveTheme(): array
    {
        $activeTheme = $this->dataArray()['activeTheme'] ?? null;
        if (!\is_array($activeTheme)) {
            return [
                'id' => '',
                'label' => '',
                'schemaVersion' => '',
                'scrollMotion' => false,
            ];
        }

        return [
            'id' => $this->stringValue($activeTheme['id'] ?? null),
            'label' => $this->stringValue($activeTheme['label'] ?? null),
            'schemaVersion' => $this->stringValue($activeTheme['schemaVersion'] ?? null),
            'scrollMotion' => filter_var($activeTheme['scrollMotion'] ?? false, \FILTER_VALIDATE_BOOL),
        ];
    }

    /**
     * @return list<array{id: string, label: string}>
     */
    public function getThemes(): array
    {
        $themes = $this->dataArray()['themes'] ?? null;
        if (!\is_array($themes)) {
            return [];
        }

        $normalized = [];
        foreach ($themes as $theme) {
            if (!\is_array($theme)) {
                continue;
            }

            $normalized[] = [
                'id' => $this->stringValue($theme['id'] ?? null),
                'label' => $this->stringValue($theme['label'] ?? null),
            ];
        }

        return $normalized;
    }

    /**
     * @return list<string>
     */
    public function getSemanticColorSlugs(): array
    {
        $slugs = $this->dataArray()['semantic_color_slugs'] ?? null;
        if (!\is_array($slugs)) {
            return [];
        }

        return array_values(array_filter($slugs, static fn (mixed $slug): bool => \is_string($slug) && $slug !== ''));
    }

    public function getGraphLayerSignature(): string
    {
        return $this->dataString('graph_layer_signature');
    }

    /**
     * @return list<array{cssVar: string, value: string, shortName: string}>
     */
    public function getColorPalette(): array
    {
        $palette = $this->dataArray()['colorPalette'] ?? null;
        if (!\is_array($palette)) {
            return [];
        }

        $normalized = [];
        foreach ($palette as $entry) {
            if (!\is_array($entry)) {
                continue;
            }

            $normalized[] = [
                'cssVar' => $this->stringValue($entry['cssVar'] ?? null),
                'value' => $this->stringValue($entry['value'] ?? null),
                'shortName' => $this->stringValue($entry['shortName'] ?? null),
            ];
        }

        return $normalized;
    }

    public function getShowcaseUrl(): ?string
    {
        $url = $this->dataArray()['showcaseUrl'] ?? null;

        return is_string($url) ? $url : null;
    }

    /**
     * @return list<array{cssVar: string, value: string, shortName: string}>
     */
    private function buildColorPalette(Theme $activeTheme): array
    {
        $tokens = $activeTheme->tokens()->all();
        $palette = [];

        foreach (ThemeTokenSchema::COLOR_KEYS as $cssVar) {
            $palette[] = [
                'cssVar' => $cssVar,
                'value' => (string) ($tokens[$cssVar] ?? ''),
                'shortName' => str_starts_with($cssVar, '--ui-color-')
                    ? substr($cssVar, \strlen('--ui-color-'))
                    : $cssVar,
            ];
        }

        return $palette;
    }

    private function resolveShowcaseUrl(): ?string
    {
        foreach (self::SHOWCASE_ROUTES as $routeName) {
            try {
                return $this->router->generate($routeName);
            } catch (RouteNotFoundException) {
                continue;
            }
        }

        return null;
    }

    private function resolveCssBytes(Request $request): int
    {
        $value = $request->attributes->get(self::CSS_BYTES_REQUEST_ATTR, 0);

        return is_int($value) ? $value : (is_numeric($value) ? (int) $value : 0);
    }

    /**
     * @return array<string, mixed>
     */
    private function dataArray(): array
    {
        if (!\is_array($this->data)) {
            return [];
        }

        /** @var array<string, mixed> $data */
        $data = $this->data;

        return $data;
    }

    private function dataString(string $key): string
    {
        return $this->stringValue($this->dataArray()[$key] ?? null);
    }

    private function dataInt(string $key): int
    {
        return $this->intValue($this->dataArray()[$key] ?? null);
    }

    private function dataBool(string $key): bool
    {
        $value = $this->dataArray()[$key] ?? false;

        return filter_var($value, \FILTER_VALIDATE_BOOL);
    }

    private function stringValue(mixed $value): string
    {
        return is_string($value) ? $value : (is_scalar($value) ? (string) $value : '');
    }

    private function intValue(mixed $value): int
    {
        return is_int($value) ? $value : (is_numeric($value) ? (int) $value : 0);
    }
}
