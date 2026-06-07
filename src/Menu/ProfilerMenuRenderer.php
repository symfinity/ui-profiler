<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Menu;

use Psr\Container\ContainerInterface;
use Twig\Environment;

final class ProfilerMenuRenderer
{
    public function __construct(
        private readonly ProfilerMenuRegistry $registry,
        private readonly Environment $twig,
        private readonly ContainerInterface $stateHandlerLocator,
        private readonly bool $enabled,
    ) {
    }

    public function render(string $collectorId, object $collector): string
    {
        if (!$this->enabled) {
            return '';
        }

        $entry = $this->registry->get($collectorId);
        if (null === $entry) {
            return '';
        }

        $disabled = false;
        $count = null;
        $status = null;

        if ($collector instanceof ProfilerMenuStateProvider) {
            $disabled = $collector->profilerMenuDisabled();
            $badge = $collector->profilerMenuBadge();
            if (null !== $badge) {
                $count = $badge['count'] ?? null;
                $status = $badge['status'] ?? null;
            }
        } else {
            $handlerId = $this->registry->getStateHandlerId($collectorId);
            if (null !== $handlerId && $this->stateHandlerLocator->has($handlerId)) {
                $handler = $this->stateHandlerLocator->get($handlerId);
                if ($handler instanceof ProfilerMenuStateHandler) {
                    $disabled = $handler->disabled($collector);
                    $badge = $handler->badge($collector);
                    if (null !== $badge) {
                        $count = $badge['count'] ?? null;
                        $status = $badge['status'] ?? null;
                    }
                }
            }
        }

        $icon = $this->resolveIcon($entry->icon);

        return $this->twig->render('@UiProfiler/_chrome/partials/collector-menu-label.html.twig', [
            'icon' => $icon,
            'title' => $entry->title,
            'disabled' => $disabled,
            'count' => $count,
            'status' => $status,
        ]);
    }

    private function resolveIcon(string $iconPath): string
    {
        if ('' === trim($iconPath)) {
            return '';
        }

        try {
            $loader = $this->twig->getLoader();
            if ($loader->exists($iconPath)) {
                return $loader->getSourceContext($iconPath)->getCode();
            }
        } catch (\Throwable) {
            // Fall through to rendered include for Twig-processed icon templates.
        }

        try {
            return trim($this->twig->render($iconPath));
        } catch (\Throwable) {
            return '';
        }
    }
}
