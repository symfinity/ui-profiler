<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Functional\Parity;

use PHPUnit\Framework\Attributes\Group;
use Symfinity\UiProfiler\Tests\ParityKernel\ParityKernel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[Group('ui-profiler-parity')]
final class ShellParityTest extends WebTestCase
{
    use ParityBrowserTrait;

    protected static function getKernelClass(): string
    {
        return ParityKernel::class;
    }

    public function testProfilerHomeRendersUiShell(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');

        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token);
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('data-ui-profiler-shell', $content);
        self::assertStringNotContainsString('data-controller="ui-profiler--dialog"', $content);
        self::assertStringContainsString('data-ui-role="grid-container"', $content);
        self::assertStringNotContainsString('class="container" data-ui-profiler-shell', $content);
        self::assertStringContainsString('data-ui-role="card"', $content);
        self::assertStringContainsString('menu-profiler', $content);
        self::assertStringContainsString('request', $content);
    }

    public function testShellUsesOwnedModuleBootstrap(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/parity/rich');

        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token . '?panel=request');
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('UiProfilerShell.boot', $content);
        self::assertStringContainsString('/_profiler/assets/js/shell.js', $content);
        self::assertStringNotContainsString('class SymfonyProfiler', $content);
        self::assertStringNotContainsString('ui-profiler/profiler/shell-chrome.css', $content);
        self::assertStringNotContainsString('ui-profiler/profiler/panel.css', $content);
        self::assertStringContainsString('data-ui-profiler-shell', $content);
        self::assertStringNotContainsString('data-controller="ui-profiler--dialog"', $content);
    }

    public function testRequestPanelExposesTabMarkupForShellRuntime(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/parity/rich');

        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token . '?panel=request');
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('data-ui-profiler-tabs', $content);
        self::assertStringContainsString('data-ui-role="tabs"', $content);
    }

    public function testRequestPanelExposesSearchableTableMarkup(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/parity/rich');

        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token . '?panel=request');
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('data-ui-profiler-table-search', $content);
    }

    public function testUiKernelSidebarUsesMenuRegistryPartial(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');

        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token . '?panel=ui_kernel');
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('data-ui-profiler-menu-label', $content);
        self::assertStringContainsString('UI Kernel', $content);
        self::assertStringNotContainsString('class="label"', $content);
        self::assertMatchesRegularExpression(
            '/data-ui-profiler-menu-icon[^>]*>[\s\S]*?<svg[\s\S]*?UI Kernel[\s\S]*?<\/span>/',
            $content,
            'UI Kernel sidebar row must include palette icon SVG',
        );
    }

    public function testShellRuntimeDefinesCopyToClipboardHandler(): void
    {
        $shellJs = (string) file_get_contents(
            \dirname(__DIR__, 3) . '/assets/js/shell.js',
        );

        self::assertStringContainsString('createCopyToClipboard', $shellJs);
        self::assertStringContainsString('[data-clipboard-text]', $shellJs);
    }

    public function testShellStylesheetDefinesProfilerGridLayout(): void
    {
        $shellCss = \Symfinity\UiProfiler\Asset\ShellChromeStylesheet::css();

        self::assertStringContainsString('[data-ui-profiler-shell] [data-ui-profiler-main][data-ui-role=grid]', $shellCss);
        self::assertStringContainsString('grid-template-columns: minmax(0, var(--ui-profiler-sidebar-width)) minmax(0, 1fr)', $shellCss);
        self::assertStringContainsString('--ui-profiler-sidebar-width: 220px', $shellCss);
        self::assertStringContainsString('--ui-profiler-shadow-panel:', $shellCss);
        self::assertStringContainsString('[data-ui-profiler-shell][data-theme$=-dark]', $shellCss);
        self::assertStringNotContainsString('color-mix(in srgb, var(--ui-color-text) 5%, transparent)', $shellCss);
        self::assertStringContainsString('[data-ui-profiler-shell] {', $shellCss);
        self::assertStringContainsString('background-color: var(--ui-color-surface)', $shellCss);
        self::assertStringNotContainsString('background-size: 15px 15px', $shellCss);
        self::assertStringContainsString('[data-ui-profiler-header-search]', $shellCss);
        self::assertStringContainsString('--ui-profiler-page-scrim', $shellCss);
        self::assertStringContainsString('0 0 0 3px var(--ui-profiler-page-scrim)', $shellCss);
        self::assertStringNotContainsString('[data-ui-profiler-header]::before', $shellCss);
        self::assertStringContainsString('[data-ui-profiler-sidebar-search][data-ui-role=search-form]', $shellCss);
        self::assertStringContainsString('[data-ui-profiler-shortcut-link]', $shellCss);
        self::assertStringContainsString('[data-ui-profiler-collector-content] [data-ui-role=filter-bar]', $shellCss);
        self::assertStringContainsString('> li.selected > a {', $shellCss);
        self::assertStringNotContainsString('> li.selected::before', $shellCss);
        self::assertStringContainsString('.log-filter > summary', $shellCss);
        self::assertStringContainsString('display: inline-flex', $shellCss);
        self::assertStringContainsString('::-webkit-details-marker', $shellCss);
        self::assertStringContainsString('[data-ui-profiler-collector-content] [data-ui-role=stat]', $shellCss);
        self::assertStringContainsString('#profiler-settings::backdrop', $shellCss);
        self::assertStringContainsString('body[data-theme$=-dark]) #profiler-settings::backdrop', $shellCss);
        self::assertStringContainsString('body[data-theme$=-light]) #profiler-settings::backdrop', $shellCss);
    }

    public function testErrorRequestSummaryUsesStockErrorRowLayout(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/parity/missing-route-for-404');

        self::assertResponseStatusCodeSame(404);

        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token);
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('data-ui-part="error-row"', $content);
        self::assertStringNotContainsString('>Response</dt>', $content);
    }

    public function testProfilerHeaderUsesSymfonyLikeTitleAndSearchField(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');

        $token = $this->requireProfilerToken($client);

        $client->request('GET', '/_profiler/' . $token);
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('data-ui-profiler-header', $content);
        self::assertMatchesRegularExpression(
            '/data-ui-profiler-header[\s\S]*<a href="[^"]*_profiler[\s\S]*Symfony Profiler[\s\S]*<\/a>/',
            $content,
        );
        self::assertStringContainsString('aria-label="Symfony"', $content);
        self::assertStringContainsString('placeholder="search on symfony.com"', $content);
        self::assertStringContainsString('action="https://symfony.com/search"', $content);
        self::assertStringContainsString('name="q"', $content);
        self::assertStringContainsString('target="_blank"', $content);
        self::assertStringContainsString('type="search"', $content);
        self::assertStringNotContainsString('data-ui-profiler-header-eyebrow', $content);
        self::assertStringNotContainsString('twig:Button', $content);
    }
}
