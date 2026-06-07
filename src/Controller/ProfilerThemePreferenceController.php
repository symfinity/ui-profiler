<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Controller;

use InvalidArgumentException;
use Symfinity\UiKernel\Theme\ActiveThemeContext;
use Symfinity\UiKernel\Theme\ThemePreferenceCookies;
use Symfinity\UiKernel\Theme\ThemePreferenceResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Persists ui-kernel theme preference from profiler settings (KTB-3).
 *
 * Uses a dedicated route so redirects stay relative (dogfood/Docker host safe)
 * and profiler panel query params are preserved without fighting _profiler routing.
 */
final class ProfilerThemePreferenceController extends AbstractController
{
    public function __construct(
        private readonly ThemePreferenceCookies $cookies,
        private readonly ThemePreferenceResolver $resolver,
        private readonly ActiveThemeContext $activeThemeContext,
    ) {
    }

    #[Route(
        '/{token}/preferences/theme',
        name: 'ui_profiler_theme_preference',
        methods: ['GET'],
    )]
    public function apply(string $token, Request $request): Response
    {
        if (!$request->query->has('scheme') && !$request->query->has('theme')) {
            return new RedirectResponse($this->profilerUrl($token, $request));
        }

        try {
            $current = $this->activeThemeContext->preferenceFromRequest($request);
            $preference = $this->resolver->applyQueryOverrides($request, $current);
        } catch (InvalidArgumentException) {
            return new RedirectResponse($this->profilerUrl($token, $request));
        }

        $response = new RedirectResponse($this->profilerUrl($token, $request));
        $this->cookies->attach($response, $preference);

        return $response;
    }

    private function profilerUrl(string $token, Request $request): string
    {
        $params = [
            'token' => $token,
            'type' => $request->query->getString('type', 'request'),
        ];

        if ($request->query->has('panel')) {
            $params['panel'] = $request->query->getString('panel');
        }

        return $this->generateUrl('_profiler', $params, UrlGeneratorInterface::ABSOLUTE_PATH);
    }
}
