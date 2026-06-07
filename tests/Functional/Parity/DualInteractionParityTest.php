<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Functional\Parity;

use PHPUnit\Framework\Attributes\Group;
use Symfinity\UiProfiler\Tests\Fixtures\ParityKernel\ParityKernelWithRuntime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[Group('ui-profiler-runtime')]
final class DualInteractionParityTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return ParityKernelWithRuntime::class;
    }

    public function testNativeShellBootstrapStillPresentWithoutStimulusRequirement(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/parity/rich');

        $token = $client->getProfile()?->getToken();
        self::assertNotNull($token);

        $client->request('GET', '/_profiler/' . $token . '?panel=request');
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('UiProfilerShell.boot', $content);
        self::assertStringNotContainsString('class SymfonyProfiler', $content);
    }

    public function testStimulusControllersWiredWhenRuntimePresent(): void
    {
        if (!class_exists(\Symfony\UX\StimulusBundle\StimulusBundle::class)) {
            self::markTestSkipped('symfony/stimulus-bundle not installed in monorepo vendor.');
        }

        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/parity/rich');

        $token = $client->getProfile()?->getToken();
        self::assertNotNull($token);

        $client->request('GET', '/_profiler/' . $token . '?panel=request');
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('data-controller="ui-profiler--dialog"', $content);
        self::assertStringContainsString('data-controller="ui-profiler--tabs ui-profiler--table-search"', $content);
    }
}
