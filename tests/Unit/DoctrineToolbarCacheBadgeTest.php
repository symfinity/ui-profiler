<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DoctrineToolbarCacheBadgeTest extends TestCase
{
    #[Test]
    public function cacheOffBadgeIsAStringAndCacheOnStillCounts(): void
    {
        $path = dirname(__DIR__, 2) . '/templates/chrome/DoctrineBundle/Collector/db.html.twig';
        $source = (string) file_get_contents($path);

        self::assertStringContainsString("status_badge('none', 'disabled')", $source);
        self::assertStringNotContainsString("status_badge('none', disabled)", $source);
        self::assertStringContainsString('cacheHitsCount', $source);
        self::assertStringContainsString('cacheMissesCount', $source);
        self::assertStringContainsString('cachePutsCount', $source);
    }
}
