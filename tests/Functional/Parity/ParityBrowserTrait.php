<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\Functional\Parity;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\Profiler\Profile;

trait ParityBrowserTrait
{
    protected function requireProfilerToken(KernelBrowser $client): string
    {
        $profile = $client->getProfile();
        self::assertNotFalse($profile);
        self::assertInstanceOf(Profile::class, $profile);

        return $profile->getToken();
    }
}
