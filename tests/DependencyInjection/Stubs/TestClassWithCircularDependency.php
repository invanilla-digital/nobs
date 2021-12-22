<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\DependencyInjection\Stubs;

class TestClassWithCircularDependency
{
    public function __construct(
        public readonly TestClassWithCircularDependency $circularDependency
    ) {
    }
}
