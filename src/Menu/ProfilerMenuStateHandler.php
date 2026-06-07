<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Menu;

/**
 * Dynamic sidebar state for Symfony ecosystem collectors (vendor DataCollector services).
 */
interface ProfilerMenuStateHandler
{
    public function disabled(object $collector): bool;

    /**
     * @return array{count?: int, status?: 'error'|'warning'}|null
     */
    public function badge(object $collector): ?array;
}
