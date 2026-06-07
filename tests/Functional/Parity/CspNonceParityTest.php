<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Functional\Parity;

use PHPUnit\Framework\Attributes\Group;
use Symfinity\UiProfiler\Tests\Fixtures\ParityKernel\ParityKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[Group('ui-profiler-parity')]
final class CspNonceParityTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return ParityKernel::class;
    }

    public function testWdtBootstrapSupportsCspNonceAttributesWhenProvided(): void
    {
        $toolbarJs = (string) file_get_contents(
            \dirname(__DIR__, 3) . '/templates/chrome/WebProfilerBundle/Profiler/toolbar_js.html.twig',
        );

        self::assertStringContainsString('csp_script_nonce', $toolbarJs);
        self::assertStringContainsString('csp_style_nonce', $toolbarJs);
        self::assertStringContainsString('type="module"', $toolbarJs);
    }

    public function testShellModuleBootstrapTemplateDeclaresOptionalScriptNonce(): void
    {
        $baseJs = (string) file_get_contents(
            \dirname(__DIR__, 3) . '/templates/chrome/WebProfilerBundle/Profiler/base_js.html.twig',
        );

        self::assertStringContainsString('csp_script_nonce', $baseJs);
        self::assertStringContainsString('type="module"', $baseJs);
    }
}
