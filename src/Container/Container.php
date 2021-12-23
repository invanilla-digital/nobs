<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Container;

use Invanilla\Nobs\Container\Exception\NotFoundException;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    #[Pure]
    public function __construct(
        private readonly ContainerCollection $items = new ContainerCollection()
    ) {
    }

    /**
     * @template T
     * @param class-string<T>|string $id
     * @return T|null|mixed
     */
    public function get(string $id): mixed
    {
        if ($this->items->offsetExists($id)) {
            return $this->items->offsetGet($id);
        }

        throw new NotFoundException(
            sprintf('Could not find item with id "%s"', $id)
        );
    }

    #[Pure]
    public function has(
        string $id
    ): bool {
        return $this->items->offsetExists($id);
    }

    public function set(string $id, mixed $value): void
    {
        $this->items->offsetSet($id, $value);
    }
}
