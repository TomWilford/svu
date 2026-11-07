<?php

declare(strict_types=1);

namespace App\Tests\Support\Trait;

use DI\ContainerBuilder;
use Selective\TestTrait\Traits\ContainerTestTrait;

trait ApplicationTestTrait
{
    use ContainerTestTrait;

    protected function setUpApp(): void
    {
        $container = new ContainerBuilder()
            ->addDefinitions(dirname(__DIR__, 3) . '/etc/app/container.php')
            ->build();

        $this->setUpContainer($container);
    }
}
