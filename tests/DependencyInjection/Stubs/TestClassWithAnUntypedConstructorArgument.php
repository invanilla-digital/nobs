<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\DependencyInjection\Stubs;

class TestClassWithAnUntypedConstructorArgument
{
    public function __construct(public $untypedArgument)
    {
    }
}
