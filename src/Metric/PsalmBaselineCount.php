<?php

declare(strict_types=1);

namespace DZunke\PanalyBaseline\Metric;

use DZunke\PanalyBaseline\Metric\Exception\BaselineNotReadable;
use DZunke\PanalyBaseline\Metric\Exception\InvalidOption;
use DZunke\PanalyBaseline\Psalm\BaselineReader;
use Panaly\Plugin\Plugin\Metric;
use Panaly\Provider\FileProvider;
use Panaly\Result\Metric\Integer;
use Panaly\Result\Metric\Value;

use function array_key_exists;
use function array_map;
use function array_sum;
use function count;
use function is_array;
use function is_string;

final class PsalmBaselineCount implements Metric
{
    public function getIdentifier(): string
    {
        return 'psalm_baseline_count';
    }

    public function getDefaultTitle(): string
    {
        return 'Psalm Baseline Count';
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

        $baseline = BaselineReader::read($baselineContent);

        if (array_key_exists('paths', $options) && is_array($options['paths']) && count($options['paths']) > 0) {
            $baseline = BaselineArrayFilter::filterFileIndexedArray($baseline, $options['paths']);
        }

        return $this->summarize($baseline);
    }

    private function summarize(array $baseline): Value
    {
        return new Integer(array_sum(array_map(
            static fn (array $byErrorTypes) => array_sum($byErrorTypes),
            $baseline,
        )));
    }
}
