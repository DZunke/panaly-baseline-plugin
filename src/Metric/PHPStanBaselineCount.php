<?php

declare(strict_types=1);

namespace DZunke\PanalyBaseline\Metric;

use DZunke\PanalyBaseline\Metric\Exception\BaselineNotReadable;
use DZunke\PanalyBaseline\Metric\Exception\InvalidOption;
use Panaly\Plugin\Plugin\Metric;
use Panaly\Provider\FileProvider;
use Panaly\Result\Metric\IntegerValue;
use Panaly\Result\Metric\Value;

use function array_key_exists;
use function array_sum;
use function count;
use function is_array;
use function is_string;
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

        try {
            $baselineContent = (new FileProvider())->read($options['baseline']);
        } catch (FileProvider\InvalidFileAccess $previous) {
            throw BaselineNotReadable::baselineLoadingFailed($options['baseline'], $previous);
        }

        if (array_key_exists('paths', $options) && is_array($options['paths']) && count($options['paths']) > 0) {
            return $this->pathFilteredSummary($baselineContent, $options['paths']);
        }

        return $this->simplePregMatchSummary($baselineContent);
    }

    /** @param list<string> $paths */
    private function pathFilteredSummary(string $baselineContent, array $paths): Value
    {
        $foundViolations = [];
        preg_match_all('/count: (\d).*\spath: (.+)$/msU', $baselineContent, $foundViolations);

        /** @var array<string, int> $result */
        $result = [];
        foreach ($foundViolations[2] as $index => $file) {
            if (! array_key_exists($file, $result)) {
                $result[$file] = (int) $foundViolations[1][$index];
                continue;
            }

            $result[$file] += $foundViolations[1][$index];
        }

        return new IntegerValue(array_sum(BaselineArrayFilter::filterFileIndexedArray($result, $paths)));
    }

    private function simplePregMatchSummary(string $baselineContent): Value
    {
        $foundCounts = [];
        preg_match_all('/count: (\d)/m', $baselineContent, $foundCounts);

        if (count($foundCounts) === 0) {
            return new IntegerValue(0);
        }

        return new IntegerValue((int) array_sum($foundCounts[1]));
    }
}
