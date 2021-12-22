<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Iterables;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;
use JetBrains\PhpStorm\Pure;

/**
 * @template T
 */
class Collection implements Countable, IteratorAggregate, ArrayAccess
{
    private array $items;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    #[Pure]
    public function count(): int
    {
        return count($this->items);
    }

    #[Pure]
    public function offsetExists(
        mixed $offset
    ): bool {
        return array_key_exists($offset, $this->items);
    }

    /**
     * @param mixed $offset
     * @return T|null
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset] ?? null;
    }

    /**
     * @param string|int $offset
     * @param T $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->toArray());
    }

    public function push(mixed $value): void
    {
        $this->items[] = $value;
    }

    #[Pure]
    public function contains(mixed $value): bool
    {
        return $this->indexOf($value) !== null;
    }

    public function indexOf(mixed $value): int|string|null
    {
        $index = array_search($value, $this->items, true);

        if ($index === false || $index === -1) {
            return null;
        }

        return $index;
    }

    public function remove(string $typeName): void
    {
        $index = $this->indexOf($typeName);

        if ($index !== null) {
            $this->offsetUnset($index);
        }
    }

    public function toArray(): array
    {
        return $this->items;
    }
}
