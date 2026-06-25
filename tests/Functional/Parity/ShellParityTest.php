<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Functional\Parity;

use PHPUnit\Framework\Attributes\Group;
use Symfinity\UiProfiler\Tests\Fixtures\ParityKernel\ParityKernel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[Group('ui-profiler-parity')]
final class ShellParityTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return ParityKernel::class;
    }

    public function testProfilerHomeRendersUiShell(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');

        $token = $client->getProfile()?->getToken();
        self::assertNotNull($token);

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

        $token = $client->getProfile()?->getToken();
        self::assertNotNull($token);

        $client->request('GET', '/_profiler/' . $token . '?panel=request');
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('UiProfilerShell.boot', $content);
        self::assertStringContainsString('/_profiler/assets/profiler/shell.js', $content);
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

        $token = $client->getProfile()?->getToken();
        self::assertNotNull($token);

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

        $token = $client->getProfile()?->getToken();
        self::assertNotNull($token);

        $client->request('GET', '/_profiler/' . $token . '?panel=request');
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('data-ui-profiler-table-search', $content);
    }

    public function testUiKernelSidebarUsesMenuRegistryPartial(): void
    {
        $client = static::createClient(['debug' => true]);
        $client->request('GET', '/');

        $token = $client->getProfile()?->getToken();
        self::assertNotNull($token);

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
            \dirname(__DIR__, 3) . '/assets/profiler/shell.js',
        );

        self::assertStringContainsString('createCopyToClipboard', $shellJs);
        self::assertStringContainsString('[data-clipboard-text]', $shellJs);
    }

    public function testShellStylesheetDefinesProfilerGridLayout(): void
    {
        $shellCss = \Symfinity\UiProfiler\Asset\ShellChromeStylesheet::CSS;

        self::assertStringContainsString('[data-ui-profiler-shell] [data-ui-profiler-main][data-ui-role="grid"]', $shellCss);
        self::assertStringContainsString('grid-template-columns: repeat(12', $shellCss);
        self::assertStringContainsString('[data-ui-profiler-shell] {', $shellCss);
        self::assertStringContainsString('background-color: var(--ui-color-surface)', $shellCss);
    }
}
