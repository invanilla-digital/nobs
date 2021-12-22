<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\DependencyInjection\Stubs;

class TestClassThatHasDependencyOnClassOnUpperLevel
{
    public function __construct(
        public readonly TestClassWithCircularDependencyNestedDeeply $upperLevelClass
    ) {
    }
}
