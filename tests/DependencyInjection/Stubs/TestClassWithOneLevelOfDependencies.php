<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\DependencyInjection\Stubs;

class TestClassWithOneLevelOfDependencies
{
    public function __construct(
        public readonly TestClassWithConstructorWithoutDependencies $classWithConstructorWithoutDependencies,
        public readonly TestClassWithNoConstructor $classWithNoConstructor
    ) {
    }
}
