<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Menu;

final readonly class ProfilerMenuEntry
{
    public function __construct(
        public string $id,
        public string $title,
        public string $icon,
        public int $priority = 0,
    ) {
    }
}
