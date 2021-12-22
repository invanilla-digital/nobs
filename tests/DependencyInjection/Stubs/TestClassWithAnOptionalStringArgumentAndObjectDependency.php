<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\DependencyInjection\Stubs;

class TestClassWithAnOptionalStringArgumentAndObjectDependency
{
    public function __construct(
        public readonly TestClassWithNoConstructor $classWithNoConstructor,
        public readonly string $optionalArgument = 'test'
    ) {
    }
}
