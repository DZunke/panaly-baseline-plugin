<?php

declare(strict_types=1);

namespace DZunke\PanalyBaseline\Metric;

use DZunke\PanalyBaseline\Metric\Exception\BaselineNotReadable;
use DZunke\PanalyBaseline\Metric\Exception\InvalidOption;
use Panaly\Plugin\Plugin\Metric;
use Panaly\Result\Metric\Integer;
use Panaly\Result\Metric\Value;

use function array_key_exists;
use function array_sum;
use function count;
use function file_get_contents;
use function is_file;
use function is_readable;
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

        if (! is_file($options['baseline']) || ! is_readable($options['baseline'])) {
            throw InvalidOption::baselineOptionMustBeAnExistingAndReadableFile($options['baseline']);
        }

        $baselineContent = file_get_contents($options['baseline']);
        if ($baselineContent === false) {
            throw BaselineNotReadable::baselineLoadingFailed($options['baseline']);
        }

        return $this->simplePregMatchSummary($options['baseline']);
    }

    private function simplePregMatchSummary(string $baselineFile): Value
    {
        $baselineContent = file_get_contents($baselineFile);
        if ($baselineContent === false) {
            throw BaselineNotReadable::baselineLoadingFailed($baselineFile);
        }

        $foundCounts = [];
        preg_match_all('/count: ([0-9])/m', $baselineContent, $foundCounts);

        if (count($foundCounts) === 0) {
            return new Integer(0);
        }

        return new Integer(array_sum($foundCounts[1]));
    }
}
