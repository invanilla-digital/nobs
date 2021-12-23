<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\DependencyInjection\Stubs;

class TestClassFactoryButMissingInterface
{
    public function make(string $php): string
    {
        return 'great again';
    }
}
