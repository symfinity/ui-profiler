<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Functional\Parity;

use PHPUnit\Framework\Attributes\Group;
use Symfinity\UiProfiler\Tests\Fixtures\ParityKernel\ParityKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[Group('ui-profiler-parity')]
final class SearchParityTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return ParityKernel::class;
    }

    public function testProfilerSearchPageRendersForFixture(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');
        self::assertResponseIsSuccessful();

        $client->followRedirects(true);
        $client->request('GET', '/_profiler/search?limit=10&type=request');
        self::assertResponseIsSuccessful();

        self::assertStringContainsString('search/results', $client->getRequest()->getRequestUri());
        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('limit=10', $content);
        self::assertStringContainsString('data-ui-role="request-status"', $content);
        self::assertStringContainsString('>Profile Search</h2>', $content);
        self::assertStringContainsString('data-ui-profiler-sidebar-search', $content);
        self::assertStringContainsString('data-ui-role="search-form"', $content);
        self::assertStringContainsString('data-ui-part="field"', $content);
        self::assertStringContainsString('data-ui-role="input"', $content);
        self::assertStringContainsString('data-ui-profiler-shortcut-link', $content);
        self::assertStringNotContainsString('<div class="status">', $content);
        self::assertStringNotContainsString('class="form-group"', $content);
        self::assertStringNotContainsString('twig:SearchForm', $content);
    }
}
