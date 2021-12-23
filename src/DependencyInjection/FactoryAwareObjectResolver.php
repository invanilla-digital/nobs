<?php

declare(strict_types=1);

namespace Invanilla\Nobs\DependencyInjection;

use Invanilla\Nobs\DependencyInjection\Exception\CircularDependencyException;
use Invanilla\Nobs\DependencyInjection\Exception\InvalidObjectFactoryException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

class FactoryAwareObjectResolver implements ObjectResolverInterface
{
    public function __construct(
        private readonly ObjectFactoryCollection $factoryCollection,
        private readonly ContainerInterface $container,
        private readonly ReflectionObjectResolver $objectResolver
    ) {
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @return T
     * @throws CircularDependencyException
     * @throws ContainerExceptionInterface
     */
    public function resolveInstance(string $className)
    {
        if ($this->container->has($className)) {
            return $this->container->get($className);
        }

        $factoryClass = $this->factoryCollection->getObjectFactoryClass($className);

        if ($factoryClass === null) {
            return $this->objectResolver->resolveInstance($className);
        }

        $factory = $this->objectResolver->resolveInstance($factoryClass);

        if (!$factory instanceof ObjectFactoryInterface) {
            throw new InvalidObjectFactoryException(
                sprintf(
                    'Factory for object class %s must implement %s',
                    $className,
                    ObjectFactoryInterface::class
                )
            );
        }

        return $this->resolveInstanceUsingFactory($factory);
    }

    private function resolveInstanceUsingFactory(ObjectFactoryInterface $factory)
    {
        return $factory->make($this);
    }
}
