<?php

namespace Invanilla\Nobs\Tests\DependencyInjection\Unit;

use Invanilla\Nobs\Container\Container;
use Invanilla\Nobs\DependencyInjection\Exception\CircularDependencyException;
use Invanilla\Nobs\DependencyInjection\ReflectionObjectResolver;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithAnOptionalStringArgument;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithAnOptionalStringArgumentAndObjectDependency;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithAnUntypedConstructorArgument;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithCircularDependency;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithCircularDependencyNestedDeeply;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithMultipleLevelsOfDependencies;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithConstructorWithoutDependencies;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithNoConstructor;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithOneLevelOfDependencies;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithTwoDependenciesRequiringTheSameDependency;
use Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestInterfaceToBeUsedAsAccessor;
use PHPUnit\Framework\TestCase;

class ReflectionObjectResolverTest extends TestCase
{
    private Container $container;
    private ReflectionObjectResolver $objectResolver;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->objectResolver = new ReflectionObjectResolver($this->container);
    }

    public function testItCanResolveAnInstanceWithoutConstructor(): void
    {
        self::assertInstanceOf(
            TestClassWithNoConstructor::class,
            $this->objectResolver->resolveInstance(TestClassWithNoConstructor::class)
        );
    }

    public function testItCanResolveAnInstanceWithConstructorWithoutDependencies(): void
    {
        $instance = $this->objectResolver->resolveInstance(TestClassWithConstructorWithoutDependencies::class);

        self::assertInstanceOf(
            TestClassWithConstructorWithoutDependencies::class,
            $instance
        );
    }

    public function testItCanResolveAClassWithSingleLevelDependencies(): void
    {
        $instance = $this->objectResolver->resolveInstance(TestClassWithOneLevelOfDependencies::class);

        self::assertInstanceOf(
            TestClassWithOneLevelOfDependencies::class,
            $instance
        );

        self::assertInstanceOf(
            TestClassWithNoConstructor::class,
            $instance->classWithNoConstructor
        );
        self::assertInstanceOf(
            TestClassWithConstructorWithoutDependencies::class,
            $instance->classWithConstructorWithoutDependencies
        );
    }

    public function testItCanResolveClassWithDeeplyNestedDependencies(): void
    {
        $instance = $this->objectResolver->resolveInstance(TestClassWithMultipleLevelsOfDependencies::class);

        self::assertInstanceOf(
            TestClassWithMultipleLevelsOfDependencies::class,
            $instance
        );

        $nestedInstance = $instance->testClassWithOneLevelOfDependencies;

        self::assertInstanceOf(TestClassWithOneLevelOfDependencies::class, $nestedInstance);

        self::assertInstanceOf(
            TestClassWithNoConstructor::class,
            $nestedInstance->classWithNoConstructor
        );
        self::assertInstanceOf(
            TestClassWithConstructorWithoutDependencies::class,
            $nestedInstance->classWithConstructorWithoutDependencies
        );
    }

    public function testItThrowsIfCircularDependencyIsDetected(): void
    {
        $this->expectException(CircularDependencyException::class);
        $this->expectExceptionMessage(
            'Circular dependency detected in following chain: Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithCircularDependency -> Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithCircularDependency'
        );

        $this->objectResolver->resolveInstance(TestClassWithCircularDependency::class);
    }

    public function testItReadsInstanceFromContainerIfExists(): void
    {
        $this->container->set(TestInterfaceToBeUsedAsAccessor::class, new TestClassWithNoConstructor());

        $instance = $this->objectResolver->resolveInstance(TestInterfaceToBeUsedAsAccessor::class);

        self::assertInstanceOf(TestClassWithNoConstructor::class, $instance);
    }

    public function testItCanResolveClassWithAConstructorArgumentThatIsAStringWithDefaultValue(): void
    {
        $instance = $this->objectResolver->resolveInstance(TestClassWithAnOptionalStringArgument::class);

        self::assertInstanceOf(TestClassWithAnOptionalStringArgument::class, $instance);
        self::assertEquals('hello world', $instance->optionalArgument);
    }

    public function testItCanResolveClassWithAConstructorArgumentThatIsAStringWithDefaultValueAndObjectDependency(
    ): void
    {
        $instance = $this->objectResolver->resolveInstance(
            TestClassWithAnOptionalStringArgumentAndObjectDependency::class
        );

        self::assertInstanceOf(TestClassWithAnOptionalStringArgumentAndObjectDependency::class, $instance);
        self::assertInstanceOf(TestClassWithNoConstructor::class, $instance->classWithNoConstructor);
        self::assertEquals('test', $instance->optionalArgument);
    }

    public function testItCanResolveAnObjectWithUntypedConstructorArgumentAndLeavingItNull(): void
    {
        $instance = $this->objectResolver->resolveInstance(TestClassWithAnUntypedConstructorArgument::class);

        self::assertInstanceOf(TestClassWithAnUntypedConstructorArgument::class, $instance);
        self::assertNull($instance->untypedArgument);
    }

    public function testItThrowsIfDeeplyNestedCircularDependencyIsDetected(): void
    {
        $this->expectException(CircularDependencyException::class);
        $this->expectExceptionMessage(
            'Circular dependency detected in following chain: Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassThatActsAsIntermediateForANestedClassWithCircularDependency -> Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassThatHasDependencyOnClassOnUpperLevel -> Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassWithCircularDependencyNestedDeeply -> Invanilla\Nobs\Tests\DependencyInjection\Stubs\TestClassThatActsAsIntermediateForANestedClassWithCircularDependency'
        );

        $this->objectResolver->resolveInstance(TestClassWithCircularDependencyNestedDeeply::class);
    }

    public function testItCanResolveInstanceWhenObjectOfTheSameInstanceIsReferencedMultipleTimes(): void
    {
        $instance = $this->objectResolver->resolveInstance(
            TestClassWithTwoDependenciesRequiringTheSameDependency::class
        );

        self::assertInstanceOf(TestClassWithTwoDependenciesRequiringTheSameDependency::class, $instance);
    }
}
