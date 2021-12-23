<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\DependencyInjection\Stubs;

use Invanilla\Nobs\DependencyInjection\ObjectFactoryInterface;
use Invanilla\Nobs\DependencyInjection\ObjectResolverInterface;

class TestClassFactory implements ObjectFactoryInterface
{
    public function make(ObjectResolverInterface $objectResolver): TestClassWithOneLevelOfDependencies
    {
        return new TestClassWithOneLevelOfDependencies(
            $objectResolver->resolveInstance(TestClassWithConstructorWithoutDependencies::class),
            $objectResolver->resolveInstance(TestClassWithNoConstructor::class)
        );
    }
}
