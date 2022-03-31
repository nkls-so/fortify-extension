<?php

declare(strict_types=1);

namespace Nkls\FortifyExtension\Tests;

use Mockery;
use Nkls\FortifyExtension\FortifyExtensionServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class OrchestraTestCase extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    protected function getPackageProviders($app)
    {
        return [FortifyExtensionServiceProvider::class];
    }
}
