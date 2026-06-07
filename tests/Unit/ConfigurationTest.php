<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfinity\UiProfiler\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testDefaultConfig(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), [[]]);

        self::assertTrue($config['enabled']);
        self::assertTrue($config['wdt']['critical_inline']);
        self::assertSame(5120, $config['wdt']['bootstrap_max_bytes']);
        self::assertTrue($config['toolbar']['dark_bar']);
    }

    public function testDisabledConfig(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), [[
            'enabled' => false,
        ]]);

        self::assertFalse($config['enabled']);
    }

    public function testLegacySfShimKeyRejected(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('legacy_sf_shim');

        (new Processor())->processConfiguration(new Configuration(), [[
            'legacy_sf_shim' => true,
        ]]);
    }
}
