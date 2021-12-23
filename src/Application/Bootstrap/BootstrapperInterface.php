<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Application\Bootstrap;

use Psr\Container\ContainerInterface;

interface BootstrapperInterface
{
    public function bootstrap(ContainerInterface $container): void;

    public function canBootstrap(ContainerInterface $container): bool;
}
