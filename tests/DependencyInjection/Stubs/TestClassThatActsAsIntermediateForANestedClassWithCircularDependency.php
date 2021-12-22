<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\DependencyInjection\Stubs;

// This really hurts me to do this, sorry if you are reading this
class TestClassThatActsAsIntermediateForANestedClassWithCircularDependency
{
    public function __construct(
        public readonly TestClassThatHasDependencyOnClassOnUpperLevel $classOnUpperLevel
    ) {
    }
}
