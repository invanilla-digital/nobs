<?php

declare(strict_types=1);

namespace Invanilla\Nobs\DependencyInjection;

use Invanilla\Nobs\Container\Container;
use Invanilla\Nobs\DependencyInjection\Exception\CircularDependencyException;
use Invanilla\Nobs\DependencyInjection\Tracking\DependencyTracker;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

class ReflectionObjectResolver implements ObjectResolverInterface
{
    public function __construct(
        private readonly Container $container
    ) {
    }

    /**
     * @inheritDoc
     * @throws CircularDependencyException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function resolveInstance(string $className)
    {
        return $this->resolveWithTracking($className, new DependencyTracker());
    }

    private function resolveWithTracking(string $className, DependencyTracker $dependencyTracker)
    {
        // When something is explicitly defined on container as null, null should be returned
        if ($this->container->has($className)) {
            return $this->container->get($className);
        }

        $reflection = new ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $this->storeDependency($className, $reflection->newInstance());
        }

        $requiredDependencyParams = $constructor->getParameters();

        if ($requiredDependencyParams === []) {
            return $this->storeDependency($className, $reflection->newInstance());
        }

        $requiredDependencies = $this->resolveInstanceDependencies(
            $requiredDependencyParams,
            $dependencyTracker
        );

        return $this->storeDependency(
            $className,
            $reflection->newInstanceArgs($requiredDependencies)
        );
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @param $instance
     * @return T
     */
    private function storeDependency(string $className, $instance)
    {
        $this->container->set($className, $instance);

        return $instance;
    }

    /**
     * @param ReflectionParameter[] $requiredDependencyParams
     */
    private function resolveInstanceDependencies(
        array $requiredDependencyParams,
        DependencyTracker $dependencyTracker
    ): array {
        $requiredDependencies = [];

        foreach ($requiredDependencyParams as $requiredDependencyParam) {
            $requiredDependencies[] = $this->resolveDependencyFromParameterByClassOrReturnDefault(
                $requiredDependencyParam,
                $dependencyTracker
            );
        }

        return $requiredDependencies;
    }

    private function resolveDependencyFromParameterByClassOrReturnDefault(
        ReflectionParameter $parameter,
        DependencyTracker $dependencyTracker
    ) {
        if (!$parameter->hasType()) {
            return null;
        }

        $type = $parameter->getType();

        if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return $parameter->isOptional() ? $parameter->getDefaultValue() : null;
        }

        $typeName = $type->getName();

        $dependencyTracker->trackDependency($typeName);

        try {
            return $this->resolveWithTracking($typeName, $dependencyTracker);
        } finally {
            $dependencyTracker->stopTracking($typeName);
        }
    }
}
