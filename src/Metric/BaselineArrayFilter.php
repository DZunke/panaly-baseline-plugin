<?php

declare(strict_types=1);

namespace DZunke\PanalyBaseline\Metric;

use Symfony\Component\Finder\Glob;

use function array_map;
use function count;
use function preg_match;

final class BaselineArrayFilter
{
    private function __construct()
    {
    }

    public static function filterFileIndexedArray(array $baselineArray, array $paths): array
    {
        if (count($baselineArray) === 0) {
            return $baselineArray;
        }

        $filteredResult = [];

        $paths = array_map(static fn (string $path) => Glob::toRegex($path), $paths);
        foreach ($paths as $pathToMatch) {
            foreach ($baselineArray as $file => $violations) {
                if (preg_match($pathToMatch, $file) === 0) {
                    continue;
                }

                $filteredResult[$file] = $violations;
            }
        }

        return $filteredResult;
    }
}
