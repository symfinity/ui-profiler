<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Functional\Parity;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Symfinity\UiProfiler\Tests\Fixtures\ParityKernel\ParityKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[Group('ui-profiler-parity')]
final class CollectorParityTest extends WebTestCase
{
    use ParityBrowserTrait;

    protected static function getKernelClass(): string
    {
        return ParityKernel::class;
    }

    /**
     * @return iterable<string, array{0: string, 1: string, 2: string, 3?: int}>
     */
    public static function coreCollectorProvider(): iterable
    {
        yield 'CO-01 request' => ['request', 'GET', '/parity/rich'];
        yield 'CO-02 time' => ['time', 'GET', '/parity/rich'];
        yield 'CO-03 memory' => ['memory', 'GET', '/parity/rich'];
        yield 'CO-04 validator' => ['validator', 'GET', '/parity/rich'];
        yield 'CO-05 form' => ['form', 'GET', '/parity/rich'];
        yield 'CO-07 logger' => ['logger', 'GET', '/parity/rich'];
        yield 'CO-08 events' => ['events', 'GET', '/parity/rich'];
        yield 'CO-09 router' => ['router', 'GET', '/parity/rich'];
        yield 'CO-10 cache' => ['cache', 'GET', '/parity/rich'];
    }

    /**
     * @return iterable<string, array{0: string, 1: string, 2: string}>
     */
    public static function extendedCollectorProvider(): iterable
    {
        yield 'CO-11 translation' => ['translation', 'GET', '/parity/rich'];
        yield 'CO-12 security' => ['security', 'GET', '/parity/rich'];
        yield 'CO-13 twig' => ['twig', 'GET', '/parity/rich'];
        yield 'CO-14 doctrine' => ['db', 'GET', '/parity/rich'];
        yield 'CO-15 mailer' => ['mailer', 'GET', '/parity/rich'];
        yield 'CO-16 messenger' => ['messenger', 'GET', '/parity/rich'];
        yield 'CO-17 http_client' => ['http_client', 'GET', '/parity/rich'];
        yield 'CO-18 serializer' => ['serializer', 'GET', '/parity/rich'];
        yield 'CO-19 config' => ['config', 'GET', '/parity/rich'];
    }

    #[DataProvider('coreCollectorProvider')]
    #[DataProvider('extendedCollectorProvider')]
    public function testCollectorPanelRenders(string $panel, string $method, string $uri, ?int $expectedStatus = null): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request($method, $uri);

        if (null !== $expectedStatus) {
            self::assertResponseStatusCodeSame($expectedStatus);
        } else {
            self::assertResponseIsSuccessful();
        }

        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token . '?panel=' . $panel);
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('data-ui-profiler-shell', $content);
        self::assertStringContainsString($panel, $content);
    }

    public function testExceptionPanelRendersForFixtureRoute(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/parity/exception');
        self::assertResponseStatusCodeSame(500);

        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token . '?panel=exception');
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('data-ui-profiler-shell', $content);
        self::assertStringContainsString('data-ui-profiler-panel="exception"', $content);
        self::assertStringContainsString('data-ui-profiler-exception-delegate', $content);
        self::assertStringContainsString('parity fixture exception', $content);
    }

    public function testFormErrorPanelRendersForFixtureRoute(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('POST', '/parity/form-error', [
            'parity_text' => ['text' => ''],
        ]);
        self::assertResponseIsSuccessful();

        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token . '?panel=form');
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('data-ui-profiler-shell', $content);
        self::assertStringContainsString('form', $content);
        self::assertMatchesRegularExpression('/error|invalid|blank/i', $content);
    }

    public function testThemeSwitcherPersistsViaQueryParameter(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->followRedirects();
        $client->request('GET', '/');
        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token . '/preferences/theme?scheme=dark');
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('data-theme="default-dark"', $content);
        self::assertStringContainsString('id="profiler-settings"', $content);
        self::assertStringContainsString('preferences/theme', $content);
        self::assertStringContainsString('scheme=dark', $content);
        self::assertStringContainsString('data-ui-variant="primary"', $content);
        self::assertStringContainsString('data-ui-role="dialog"', $content);
        self::assertStringNotContainsString('settings-modal-shadow', $content);
        self::assertStringNotContainsString('ui-profiler-shell__theme-switcher', $content);
        self::assertStringNotContainsString('ui_profiler_theme', $content);
        self::assertStringNotContainsString('symfony/profiler/theme', $content);

        $client->request('GET', '/_profiler/' . $token);
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('data-theme="default-dark"', (string) $client->getResponse()->getContent());
    }

    public function testProfilerShellUsesAssetMapperAndOwnedShellChrome(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');
        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token);
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('ui-kernel-theme-css', $content);
        self::assertStringNotContainsString('ui-profiler/profiler/shell-chrome.css', $content);
        self::assertStringNotContainsString('ui-profiler/profiler/panel.css', $content);
        self::assertStringContainsString('data-ui-profiler-shell', $content);
    }

    public function testEventsListenerCellsDoNotWrapNamesInDoubleQuotes(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/parity/rich');
        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token . '?panel=events');
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('bootEventsPanel', $content);
        self::assertStringContainsString('events-panel.js', $content);
        self::assertStringContainsString('Called', $content);

        $names = [];
        if (preg_match_all('/events-listener-name[^>]*>([^<]+)</', $content, $codeMatches)) {
            $names = $codeMatches[1];
        } elseif (preg_match_all('/class="sf-dump-(?:note|str)[^"]*"[^>]*>([^<]+)</', $content, $dumpMatches)) {
            $names = $dumpMatches[1];
        }

        self::assertGreaterThan(10, \count($names), 'Expected many rendered listener names in the Events panel (events-listener-name)');

        foreach ($names as $name) {
            $name = html_entity_decode(trim($name), \ENT_QUOTES | \ENT_HTML5);
            self::assertStringStartsNotWith('"', $name, 'Listener name must not start with a double quote');
            self::assertStringEndsNotWith('"', $name, 'Listener name must not end with a double quote');
        }
    }

    public function testLoggerPanelEmitsFilterJavascript(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/parity/rich');
        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token . '?panel=logger');
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('SymfonyProfilerLoggerPanel', $content);
        self::assertStringContainsString('#updateLogsTable', $content);
    }

    public function testKernelThemeCssIsInjectedInShell(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');
        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token);
        self::assertResponseIsSuccessful();

        self::assertStringContainsString('ui-kernel-theme-css', (string) $client->getResponse()->getContent());
    }
}
