<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\ParityKernel;

use Symfinity\UiProfiler\UiProfilerBundle;
use Symfony\UX\StimulusBundle\StimulusBundle;

/** Parity kernel with Stimulus for DI-T2 (@group ui-profiler-runtime). */
final class ParityKernelWithRuntime extends ParityKernel
{
    public function registerBundles(): array
    {
        $bundles = [];

        foreach (parent::registerBundles() as $bundle) {
            if ($bundle instanceof UiProfilerBundle) {
                $bundles[] = new StimulusBundle();
            }

            $bundles[] = $bundle;
        }

        return $bundles;
    }
}
