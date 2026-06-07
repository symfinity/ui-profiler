<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Menu;

final class ProfilerMenuRegistry
{
    /** @var array<string, ProfilerMenuEntry> */
    private array $entries = [];

    /** @var array<string, string> */
    private array $stateHandlerIds = [];

    /**
     * @param array<string, array{title: string, icon: string, priority?: int}> $entries
     * @param array<string, string>                                              $stateHandlerIds
     */
    public function __construct(array $entries = [], array $stateHandlerIds = [])
    {
        foreach ($entries as $id => $config) {
            if ('' === ($config['title'] ?? '') || '' === ($config['icon'] ?? '')) {
                continue;
            }

            $this->entries[$id] = new ProfilerMenuEntry(
                $id,
                $config['title'],
                $config['icon'],
                (int) ($config['priority'] ?? 0),
            );
        }

        $this->stateHandlerIds = $stateHandlerIds;
    }

    public function has(string $collectorId): bool
    {
        return isset($this->entries[$collectorId]);
    }

    public function get(string $collectorId): ?ProfilerMenuEntry
    {
        return $this->entries[$collectorId] ?? null;
    }

    public function getStateHandlerId(string $collectorId): ?string
    {
        return $this->stateHandlerIds[$collectorId] ?? null;
    }
}
