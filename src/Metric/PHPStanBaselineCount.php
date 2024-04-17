<?php

declare(strict_types=1);

namespace DZunke\PanalyBaseline\Metric;

use DZunke\PanalyBaseline\Metric\Exception\BaselineNotReadable;
use DZunke\PanalyBaseline\Metric\Exception\InvalidOption;
use Panaly\Plugin\Plugin\Metric;
use Panaly\Result\Metric\Integer;
use Panaly\Result\Metric\Value;
use Symfony\Component\Finder\Glob;

use function array_key_exists;
use function array_map;
use function array_sum;
use function assert;
use function count;
use function file_get_contents;
use function is_array;
use function is_file;
use function is_readable;
use function is_string;
use function preg_match;
use function preg_match_all;

final class PHPStanBaselineCount implements Metric
{
    public function getIdentifier(): string
    {
        return 'phpstan_baseline_count';
    }

    public function getDefaultTitle(): string
    {
        return 'PHPStan Baseline Count';
    }

    public function calculate(array $options): Value
    {
        if (! array_key_exists('baseline', $options) || ! is_string($options['baseline']) || $options['baseline'] === '') {
            $baseline = $options['baseline'] ?? '';

            throw InvalidOption::baselineOptionMustBeGiven((string) $baseline);
        }

        if (! is_file($options['baseline']) || ! is_readable($options['baseline'])) {
            throw InvalidOption::baselineOptionMustBeAnExistingAndReadableFile($options['baseline']);
        }

        $baselineContent = file_get_contents($options['baseline']);
        if ($baselineContent === false) {
            throw BaselineNotReadable::baselineLoadingFailed($options['baseline']);
        }

        if (array_key_exists('paths', $options) && is_array($options['paths']) && count($options['paths']) > 0) {
            return $this->pathFilteredSummary($options['baseline'], $options['paths']);
        }

        return $this->simplePregMatchSummary($options['baseline']);
    }

    /**
     * @param non-empty-string $baseline
     * @param string[]         $paths
     */
    private function pathFilteredSummary(string $baseline, array $paths): Value
    {
        $baselineContent = file_get_contents($baseline);
        if ($baselineContent === false) {
            throw BaselineNotReadable::baselineLoadingFailed($baseline);
        }

        $foundViolations = [];
        preg_match_all('/count: (\d).*\spath: (.+)$/msU', $baselineContent, $foundViolations);

        /** @var array<string, int> $result */
        $result = [];
        foreach ($foundViolations[2] as $index => $file) {
            assert(is_string($file));

            if (! array_key_exists($file, $result)) {
                $result[$file] = (int) $foundViolations[1][$index];
                continue;
            }

            $result[$file] += $foundViolations[1][$index];
        }

        // Filter the result for only files that should be summed up
        $filteredResult = [];

        $paths = array_map(static fn (string $path) => Glob::toRegex($path), $paths);
        foreach ($paths as $pathToMatch) {
            foreach ($result as $file => $count) {
                if (preg_match($pathToMatch, $file) === 0) {
                    continue;
                }

                $filteredResult[$file] = $count;
            }
        }

        return new Integer(array_sum($filteredResult));
    }

    private function simplePregMatchSummary(string $baselineFile): Value
    {
        $baselineContent = file_get_contents($baselineFile);
        if ($baselineContent === false) {
            throw BaselineNotReadable::baselineLoadingFailed($baselineFile);
        }

        $foundCounts = [];
        preg_match_all('/count: (\d)/m', $baselineContent, $foundCounts);

        if (count($foundCounts) === 0) {
            return new Integer(0);
        }

        return new Integer(array_sum($foundCounts[1]));
    }
}
