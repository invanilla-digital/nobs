<?php

declare(strict_types=1);

namespace Invanilla\Nobs\DependencyInjection;

interface ObjectResolverInterface
{
    /**
     * @template T
     * @param class-string<T> $className
     * @return T|null
     */
    public function resolveInstance(string $className);
}
