<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\ParityKernel;

final class ParityMessage
{
    public function __construct(
        public readonly string $payload = 'parity-fixture',
    ) {
    }
}
