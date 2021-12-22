<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\DependencyInjection\Stubs;

class TestClassWithMultipleLevelsOfDependencies
{
    public function __construct(
        public readonly TestClassWithOneLevelOfDependencies $testClassWithOneLevelOfDependencies
    ) {
    }
}
