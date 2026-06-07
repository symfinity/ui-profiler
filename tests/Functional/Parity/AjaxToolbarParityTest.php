<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Functional\Parity;

use PHPUnit\Framework\Attributes\Group;
use Symfinity\UiProfiler\Tests\Fixtures\ParityKernel\ParityKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[Group('ui-profiler-parity')]
final class AjaxToolbarParityTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return ParityKernel::class;
    }

    public function testToolbarIncludesAjaxCollectorBlock(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');

        $token = $client->getProfile()?->getToken();
        self::assertNotNull($token);

        $client->request('GET', '/_wdt/' . $token);
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('sf-toolbar-block-ajax', $content);
        self::assertStringContainsString('sf-toolbar-ajax-requests', $content);
    }

    public function testAjaxFixtureRouteReturnsJson(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/parity/ajax');

        self::assertResponseIsSuccessful();
        self::assertJson((string) $client->getResponse()->getContent());
    }

    public function testToolbarBootstrapUsesModuleRuntimeWithoutEval(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        $content = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('type="module"', $content);
        self::assertStringNotContainsString('eval.call', $content);
        self::assertStringContainsString('sfwdt', $content);
    }
}
