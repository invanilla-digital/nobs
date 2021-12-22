<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\DependencyInjection\Stubs;

class TestClassWithTwoDependenciesRequiringTheSameDependency
{
    public function __construct(
        public readonly TestClassWithOneLevelOfDependencies $classWithOneLevelOfDependencies,
        public readonly TestClassWithMultipleLevelsOfDependencies $classWithMultipleLevelsOfDependencies,
    ) {
    }
}
