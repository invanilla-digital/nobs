<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\DependencyInjection\Stubs;

class TestClassWithCircularDependencyNestedDeeply
{
    public function __construct(
        public readonly TestClassThatActsAsIntermediateForANestedClassWithCircularDependency $intermediateClass
    ) {
    }
}
