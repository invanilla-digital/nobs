<?php

declare(strict_types=1);

namespace Invanilla\Nobs\DependencyInjection;

/**
 * @template T
 */
interface ObjectFactoryInterface
{
    /**
     * @param ObjectResolverInterface $objectResolver
     * @return T
     */
    public function make(ObjectResolverInterface $objectResolver);
}
