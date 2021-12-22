<?php

declare(strict_types=1);

namespace Invanilla\Nobs\DependencyInjection;

use Invanilla\Nobs\DependencyInjection\Exception\CircularDependencyException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

class ReflectionObjectResolver implements ObjectResolverInterface
{
    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function resolveInstance(string $className)
    {
        // When something is explicitly defined on container as null, null should be returned
        if ($this->container->has($className)) {
            return $this->container->get($className);
        }

        $reflection = new ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $requiredDependencyParams = $constructor->getParameters();

        if ($requiredDependencyParams === []) {
            return $reflection->newInstance();
        }

        $requiredDependencies = $this->resolveInstanceDependencies($className, $requiredDependencyParams);

        return $reflection->newInstanceArgs($requiredDependencies);
    }

    /**
     * @param ReflectionParameter[] $requiredDependencyParams
     */
    private function resolveInstanceDependencies(string $parentClassName, array $requiredDependencyParams): array
    {
        $requiredDependencies = [];

        foreach ($requiredDependencyParams as $requiredDependencyParam) {
            $requiredDependencies[] = $this->resolveDependencyFromParameterByClassOrReturnDefault(
                $parentClassName,
                $requiredDependencyParam
            );
        }

        return $requiredDependencies;
    }

    private function resolveDependencyFromParameterByClassOrReturnDefault(
        string $parentClassName,
        ReflectionParameter $parameter
    ) {
        if (!$parameter->hasType()) {
            return null;
        }

        $type = $parameter->getType();

        if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return $parameter->isOptional() ? $parameter->getDefaultValue() : null;
        }

        if ($type->getName() === $parentClassName) {
            throw new CircularDependencyException(
                sprintf('Class %s contains nested circular dependency', $parentClassName)
            );
        }

        return $this->resolveInstance($type->getName());
    }
}
