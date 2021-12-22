<?php

namespace Invanilla\Nobs\Tests\Iterables\Unit;

use ArrayIterator;
use Invanilla\Nobs\Iterables\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    private Collection $collection;

    protected function setUp(): void
    {
        $this->collection = new Collection(
            [
                'code' => 'is good',
                'php' => 'is love',
                'frameworks' => 'too many'
            ]
        );
    }

    public function testOffsetSet(): void
    {
        self::assertFalse($this->collection->offsetExists('nobs'));
        self::assertFalse(isset($this->collection['nobs']));

        self::assertFalse($this->collection->offsetExists('bobs'));
        self::assertFalse(isset($this->collection['bobs']));

        $this->collection['nobs'] = 'rocks';
        $this->collection->offsetSet('bobs', 'jobs');

        self::assertEquals('rocks', $this->collection->offsetGet('nobs'));
        self::assertEquals('jobs', $this->collection['bobs']);
    }

    public function testCount(): void
    {
        self::assertCount(3, $this->collection);
        self::assertEquals(3, $this->collection->count());
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->collection->getIterator());

        $loops = 0;

        foreach ($this->collection as $item) {
            $loops++;
        }

        self::assertEquals(3, $loops);
    }

    public function testOffsetUnset(): void
    {
        self::assertEquals('is good', $this->collection->offsetGet('code'));
        self::assertEquals('is love', $this->collection->offsetGet('php'));

        unset($this->collection['code']);
        $this->collection->offsetUnset('php');

        self::assertFalse($this->collection->offsetExists('code'));
        self::assertFalse($this->collection->offsetExists('php'));
    }

    public function testOffsetGet(): void
    {
        self::assertEquals('is love', $this->collection['php']);
        self::assertEquals('is good', $this->collection->offsetGet('code'));
    }

    public function testOffsetExists(): void
    {
        self::assertTrue(
            $this->collection->offsetExists('php')
        );
        self::assertTrue(
            isset($this->collection['php'])
        );

        self::assertFalse(
            $this->collection->offsetExists('i should not exist')
        );

        self::assertFalse(
            isset($this->collection['i should not exist'])
        );
    }

    public function testToArray(): void
    {
        self::assertSame(
            [
                'code' => 'is good',
                'php' => 'is love',
                'frameworks' => 'too many'
            ],
            $this->collection->toArray()
        );
    }
}
