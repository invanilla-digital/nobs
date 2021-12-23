<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Event;

interface ListenerInterface
{
    public function __invoke($event): void;
}
