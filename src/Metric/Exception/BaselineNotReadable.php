<?php

declare(strict_types=1);

namespace DZunke\PanalyPHPMD\Metric\Exception;

use RuntimeException;
use Throwable;

class BaselineNotReadable extends RuntimeException
{
    public static function baselineLoadingFailed(string $baseline, Throwable|null $previous = null): BaselineNotReadable
    {
        return new self(
            message: 'The given baseline file "' . $baseline . '" could not be loaded.',
            previous: $previous,
        );
    }
}
