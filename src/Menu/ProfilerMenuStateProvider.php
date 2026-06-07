<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Menu;

/**
 * Optional dynamic sidebar state when the data_collector service implements this interface.
 */
interface ProfilerMenuStateProvider
{
    public function profilerMenuDisabled(): bool;

    /**
     * @return array{count?: int, status?: 'error'|'warning'}|null
     */
    public function profilerMenuBadge(): ?array;
}
