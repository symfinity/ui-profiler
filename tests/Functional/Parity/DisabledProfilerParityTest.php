<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Functional\Parity;

use PHPUnit\Framework\Attributes\Group;
use Symfinity\UiProfiler\Tests\Fixtures\ParityKernel\DisabledParityKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[Group('ui-profiler-parity')]
final class DisabledProfilerParityTest extends WebTestCase
{
    use ParityBrowserTrait;

    protected static function getKernelClass(): string
    {
        return DisabledParityKernel::class;
    }

    public function testDisabledConfigUsesStockProfilerShellMarkers(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');

        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token);
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringNotContainsString('data-ui-profiler-shell', $content);
        self::assertStringContainsString('menu-profiler', $content);
    }
}
