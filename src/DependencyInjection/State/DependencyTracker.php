<?php

declare(strict_types=1);

namespace Invanilla\Nobs\DependencyInjection\State;

use Invanilla\Nobs\DependencyInjection\Exception\CircularDependencyException;
use Invanilla\Nobs\Iterables\Collection;
use JetBrains\PhpStorm\Pure;

/**
 * @internal
 */
final class DependencyTracker
{
    #[Pure]

    public function __construct(
        private readonly Collection $dependencies = new Collection()
    ) {
    }

    public function trackDependency(string $dependentClass): void
    {
        if ($this->dependencies->contains($dependentClass)) {
            $this->dependencies->push($dependentClass);

            throw new CircularDependencyException(
                sprintf(
                    'Circular dependency detected in following chain: %s',
                    implode(' -> ', $this->dependencies->toArray())
                )
            );
        }

        $this->dependencies->push($dependentClass);
    }

    public function stopTracking(string $typeName): void
    {
        $this->dependencies->remove($typeName);
    }
}
