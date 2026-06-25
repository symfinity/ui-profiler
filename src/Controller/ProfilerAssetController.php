<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Serves owned profiler ES modules under /_profiler/assets/* — avoids AssetMapper 404 in dogfood/dev.
 *
 * CSS stays on /_wdt/styles (toolbar.css.twig override). JS lives under assets/js/; Stimulus under assets/controllers/.
 */
final class ProfilerAssetController extends AbstractController
{
    /** @var array<string, true> */
    private const ALLOWED = [
        'js/wdt.js' => true,
        'js/shell.js' => true,
        'js/panels/time-timeline.js' => true,
        'js/panels/form-panel.js' => true,
        'js/panels/events-panel.js' => true,
    ];

    public function __construct(
        private readonly string $bundlePath,
    ) {
    }

    #[Route(
        '/_profiler/assets/{assetPath}',
        name: 'ui_profiler_asset',
        requirements: ['assetPath' => 'js/wdt\.js|js/shell\.js|js/panels/(time-timeline|form-panel|events-panel)\.js'],
        methods: ['GET'],
    )]
    public function __invoke(string $assetPath): Response
    {
        if (!isset(self::ALLOWED[$assetPath])) {
            throw new NotFoundHttpException(sprintf('Unknown ui-profiler asset "%s".', $assetPath));
        }

        $file = $this->bundlePath.'/assets/'.$assetPath;
        if (!is_file($file) || !is_readable($file)) {
            throw new NotFoundHttpException(sprintf('ui-profiler asset missing: %s', $assetPath));
        }

        return new Response(
            (string) file_get_contents($file),
            Response::HTTP_OK,
            [
                'Content-Type' => 'text/javascript; charset=UTF-8',
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ],
        );
    }
}
