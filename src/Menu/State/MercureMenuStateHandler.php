<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Menu\State;

use Symfinity\UiProfiler\Menu\ProfilerMenuStateHandler;

final class MercureMenuStateHandler implements ProfilerMenuStateHandler
{
    public function disabled(object $collector): bool
    {
        $count = $collector->count ?? null;

        return null !== $count && 0 === (int) $count;
    }

    public function badge(object $collector): ?array
    {
        return null;
    }
}
