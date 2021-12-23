<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Tests\DependencyInjection\Unit;

use Invanilla\Nobs\Container\Container;
use Invanilla\Nobs\DependencyInjection\Exception\InvalidObjectFactoryException;
use Invanilla\Nobs\DependencyInjection\FactoryAwareObjectResolver;
use Invanilla\Nobs\DependencyInjection\ObjectFactoryCollection;
use Invanilla\Nobs\DependencyInjection\ReflectionObjectResolver;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassFactory;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassFactoryButMissingInterface;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithNoConstructor;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithOneLevelOfDependencies;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestInterfaceToBeUsedAsAccessor;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class FactoryAwareObjectResolverTest extends TestCase
{
    private ContainerInterface $container;
    private ObjectFactoryCollection $factoryCollection;
    private FactoryAwareObjectResolver $objectResolver;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->factoryCollection = new ObjectFactoryCollection();
        $this->objectResolver = new FactoryAwareObjectResolver(
            $this->factoryCollection,
            $this->container,
            new ReflectionObjectResolver($this->container)
        );
    }

    public function testItCanCreateAnObjectUsingFactory(): void
    {
        $this->factoryCollection->setObjectFactoryClass(
            TestInterfaceToBeUsedAsAccessor::class,
            TestClassFactory::class
        );

        self::assertInstanceOf(
            TestClassWithOneLevelOfDependencies::class,
            $this->objectResolver->resolveInstance(TestInterfaceToBeUsedAsAccessor::class)
        );
    }

    public function testItIsAwareOfCachedDependenciesInContainer(): void
    {
        $testObject = (object)['test'];
        $this->container->set(TestInterfaceToBeUsedAsAccessor::class, $testObject);

        self::assertSame($testObject, $this->objectResolver->resolveInstance(TestInterfaceToBeUsedAsAccessor::class));
    }

    public function testItFallbacksToReflectionResolver(): void
    {
        self::assertInstanceOf(
            TestClassWithNoConstructor::class,
            $this->objectResolver->resolveInstance(TestClassWithNoConstructor::class)
        );
    }

    public function testItThrowsIfObjectFactoryDoesNotImplementObjectFactoryInterface(): void
    {
        $this->expectException(InvalidObjectFactoryException::class);
        $this->expectExceptionMessage(
            'Factory for object class Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestInterfaceToBeUsedAsAccessor must implement Invanilla\Nobs\DependencyInjection\ObjectFactoryInterface'
        );

        $this->factoryCollection->setObjectFactoryClass(
            TestInterfaceToBeUsedAsAccessor::class,
            TestClassFactoryButMissingInterface::class
        );

        $this->objectResolver->resolveInstance(TestInterfaceToBeUsedAsAccessor::class);
    }
}
