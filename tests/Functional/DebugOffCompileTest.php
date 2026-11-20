<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfinity\UiKernel\UiKernelBundle;
use Symfinity\UiProfiler\Tests\Functional\DebugOff\DebugOffProfilerKernel;
use Symfinity\UiProfiler\UiProfilerBundle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Dev kernel with the profiler on and debug off must compile (collectors dropped before ProfilerPass).
 */
final class DebugOffCompileTest extends TestCase
{
    /**
     * ui-profiler always runs with UiKernelBundle registered (its controllers use ui-kernel services),
     * so this covers both collectors: ui-profiler's and ui-kernel's.
     */
    #[Test]
    public function containerCompilesWithUiKernelAndUiProfiler(): void
    {
        $kernel = new DebugOffProfilerKernel('both', [new UiKernelBundle(), new UiProfilerBundle()], [
            'symfinity_ui_kernel' => ['default_theme' => 'default'],
            'symfinity_ui_profiler' => ['enabled' => true],
        ]);
        (new Filesystem())->remove($kernel->getCacheDir());

        $kernel->boot();

        self::assertTrue($kernel->getContainer()->has('profiler'));
        $kernel->shutdown();
    }
}
