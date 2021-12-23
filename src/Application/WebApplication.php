<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Application;

use Invanilla\Nobs\Application\Bootstrap\BootstrapperInterface;
use Invanilla\Nobs\Application\Bootstrap\SlimHttpBootstrapper;
use Invanilla\Nobs\Application\Exception\ApplicationException;
use Invanilla\Nobs\Application\Exception\BootstrapException;
use Invanilla\Nobs\Application\Kernel\KernelInterface;
use Invanilla\Nobs\Container\Container;
use Invanilla\Nobs\DependencyInjection\ObjectResolverInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class WebApplication implements ApplicationInterface
{
    public function __construct(
        private readonly ContainerInterface $container = new Container(),
        private readonly BootstrapperInterface $bootstrapper = new SlimHttpBootstrapper()
    ) {
        $this->validateConfiguration();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(): void
    {
        $this->bootstrap();

        $this->getKernel()->start();
    }

    public function getKernel(): KernelInterface
    {
        return $this->container
            ->get(ObjectResolverInterface::class)
            ->resolveInstance(KernelInterface::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function bootstrap(): void
    {
        $this->bootstrapper->bootstrap($this->container);

        if (!$this->container->has(ObjectResolverInterface::class)) {
            throw new ApplicationException(
                sprintf(
                    '%s cannot run without %s. Please define required dependency on the bootstrapper',
                    self::class,
                    ObjectResolverInterface::class
                )
            );
        }

        if (!$this->container->has(KernelInterface::class)) {
            throw new ApplicationException(
                sprintf(
                    '%s cannot run without %s. Please define required dependency on the bootstrapper',
                    self::class,
                    KernelInterface::class
                )
            );
        }
    }

    /**
     * @return void
     */
    private function validateConfiguration(): void
    {
        if (!$this->bootstrapper->canBootstrap($this->container)) {
            throw new BootstrapException(
                sprintf(
                    'Container of class %s cannot be bootstrapped by %s',
                    $this->container::class,
                    $this->bootstrapper::class
                )
            );
        }
    }
}
