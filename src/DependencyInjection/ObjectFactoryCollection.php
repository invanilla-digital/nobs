<?php

declare(strict_types=1);

namespace Invanilla\Nobs\DependencyInjection;

use Invanilla\Nobs\Iterables\Collection;

class ObjectFactoryCollection extends Collection
{
    /**
     * @param class-string $className
     * @return class-string<ObjectFactoryInterface>|null
     */
    public function getObjectFactoryClass(string $className): ?string
    {
        return $this->offsetGet($className);
    }

    /**
     * @param class-string $className
     * @param class-string $factoryClassName
     * @return void
     */
    public function setObjectFactoryClass(string $className, string $factoryClassName): void
    {
        $this->offsetSet($className, $factoryClassName);
    }
}

