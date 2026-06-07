<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Menu\State;

use Symfinity\UiProfiler\Menu\ProfilerMenuStateHandler;

final class TwigComponentMenuStateHandler implements ProfilerMenuStateHandler
{
    public function disabled(object $collector): bool
    {
        $components = $collector->components ?? null;

        return \is_array($components) && [] === $components;
    }

    public function badge(object $collector): ?array
    {
        return null;
    }
}
