<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\Container\Unit;

use Invanilla\Nobs\Container\Container;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;

class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testItThrowsExceptionWhenItemNotFoundInContainer(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $this->expectErrorMessage('Could not find item with id "nobs"');

        $this->container->get('nobs');
    }

    public function testItReturnsItemFromContainerById(): void
    {
        $this->container->set('nobs', 'hello world');

        self::assertEquals('hello world', $this->container->get('nobs'));
    }

    public function testItCanTellWhetherItContainsAnItemWithId(): void
    {
        $this->container->set('nobs', 'hello world');
        $this->container->set('explicit-null', null);

        self::assertFalse($this->container->has('bad_code'));
        self::assertTrue($this->container->has('nobs'));
        self::assertTrue($this->container->has('explicit-null'));
    }
}
