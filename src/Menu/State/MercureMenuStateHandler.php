<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Menu\State;

use Symfinity\UiProfiler\Menu\ProfilerMenuStateHandler;

final class MercureMenuStateHandler implements ProfilerMenuStateHandler
{
    public function disabled(object $collector): bool
    {
        if (!\property_exists($collector, 'count') || !\is_int($collector->count)) {
            return false;
        }

        return 0 === $collector->count;
    }

    public function badge(object $collector): ?array
    {
        return null;
    }
}
