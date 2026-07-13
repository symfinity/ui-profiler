<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\ParityKernel;

use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class ParityTestHttpClient extends MockHttpClient
{
    public function __construct()
    {
        parent::__construct([new MockResponse('{"status":"ok"}', ['http_code' => 200])]);
    }
}
