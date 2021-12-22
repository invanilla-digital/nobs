<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\DependencyInjection\Stubs;

class TestClassWithAnOptionalStringArgument
{
    public function __construct(
        public readonly string $optionalArgument = 'hello world'
    ) {
    }
}
