<?php

declare(strict_types=1);

$packageRoot = __DIR__;
$monorepoRoot = dirname($packageRoot, 2);
$monorepoAutoload = $monorepoRoot.'/vendor/autoload.php';
$packageAutoload = $packageRoot.'/vendor/autoload.php';

if (is_file($monorepoRoot.'/mono.json') && is_file($monorepoAutoload)) {
    require $monorepoAutoload;
} elseif (is_file($packageAutoload)) {
    require $packageAutoload;
} else {
    throw new RuntimeException('Composer autoload not found — run composer install in the package or monorepo.');
}

require __DIR__.'/phpstan-stubs.php';
