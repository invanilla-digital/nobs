<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Application\Bootstrap;

use Invanilla\Nobs\Application\Kernel\HttpKernel;
use Invanilla\Nobs\Application\Kernel\KernelInterface;
use Invanilla\Nobs\Container\Container;
use Invanilla\Nobs\DependencyInjection\FactoryAwareObjectResolver;
use Invanilla\Nobs\DependencyInjection\ObjectFactoryCollection;
use Invanilla\Nobs\DependencyInjection\ObjectResolverInterface;
use Invanilla\Nobs\DependencyInjection\ReflectionObjectResolver;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Only works with NOBS container
 * @see Container
 */
class SlimHttpBootstrapper implements BootstrapperInterface
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function bootstrap(ContainerInterface|Container $container): void
    {
        $reflectionClassObjectResolver = new ReflectionObjectResolver($container);

        $this->defineContainer($container);
        $this->defineObjectResolvers($container, $reflectionClassObjectResolver);
        $this->defineKernel($container, $container->get(ObjectResolverInterface::class));
    }

    public function canBootstrap(ContainerInterface $container): bool
    {
        return $container instanceof Container;
    }

    private function defineContainer(Container $container): void
    {
        $container->set(ContainerInterface::class, $container);
        $container->set(Container::class, $container);
    }

    private function defineObjectResolvers(
        Container $container,
        ReflectionObjectResolver $reflectionClassObjectResolver
    ): void {
        $container->set(ObjectFactoryCollection::class, new ObjectFactoryCollection());
        $container->set(ReflectionObjectResolver::class, $reflectionClassObjectResolver);
        $container->set(
            ObjectResolverInterface::class,
            $reflectionClassObjectResolver->resolveInstance(FactoryAwareObjectResolver::class)
        );
    }

    private function defineKernel(
        Container $container,
        ObjectResolverInterface $objectResolver
    ): void {
        $container->set(
            KernelInterface::class,
            $objectResolver->resolveInstance(HttpKernel::class)
        );
    }
}
