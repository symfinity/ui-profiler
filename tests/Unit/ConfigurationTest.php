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
        self::assertTrue($config['wdt']['critical_css_inline']);
        self::assertTrue($config['wdt']['module_preload']);
        self::assertFalse($config['legacy_sf_shim']);
    }

    public function testDisabledConfig(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), [[
            'enabled' => false,
        ]]);

        self::assertFalse($config['enabled']);
    }

    public function testLegacySfShimHardFailsWhenTrue(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('legacy_sf_shim');

        (new Processor())->processConfiguration(new Configuration(), [[
            'legacy_sf_shim' => true,
        ]]);
    }
}
