<?php

declare(strict_types=1);

namespace DZunke\PanalyBaseline\Metric\Exception;

use InvalidArgumentException;

class InvalidOption extends InvalidArgumentException
{
    public static function baselineOptionMustBeGiven(string $baseline): InvalidOption
    {
        return new self('The baseline option must be a valid non-empty string.');
    }

    public static function baselineOptionMustBeAnExistingAndReadableFile(string $baseline): InvalidOption
    {
        return new self('The given baseline "' . $baseline . '" is not an existing file or not readable.');
    }
}
