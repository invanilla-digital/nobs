<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Application;

use Invanilla\Nobs\Application\Kernel\KernelInterface;

interface ApplicationInterface
{
    public function getKernel(): KernelInterface;
}
