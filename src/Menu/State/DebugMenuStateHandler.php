<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Menu\State;

use Symfinity\UiProfiler\Menu\ProfilerMenuStateHandler;

final class DebugMenuStateHandler implements ProfilerMenuStateHandler
{
    public function disabled(object $collector): bool
    {
        if (!\property_exists($collector, 'dumpsCount') || !\is_int($collector->dumpsCount)) {
            return false;
        }

        return 0 === $collector->dumpsCount;
    }

    public function badge(object $collector): ?array
    {
        return null;
    }
}
