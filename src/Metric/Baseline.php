<?php

declare(strict_types=1);

namespace DZunke\PanalyPHPMD\Metric;

use DZunke\PanalyPHPMD\Metric\Exception\BaselineNotReadable;
use DZunke\PanalyPHPMD\Metric\Exception\InvalidOption;
use Panaly\Plugin\Plugin\Metric;
use Panaly\Result\Metric\Integer;
use Panaly\Result\Metric\Value;
use PHPMD\Baseline\BaselineSetFactory;
use PHPMD\Baseline\ViolationBaseline;
use ReflectionClass;
use ReflectionProperty;
use Throwable;

use function array_key_exists;
use function array_map;
use function array_sum;
use function assert;
use function class_exists;
use function count;
use function in_array;
use function is_array;
use function is_string;

final class Baseline implements Metric
{
    public function getIdentifier(): string
    {
        return 'phpmd_baseline_count';
    }

    public function getDefaultTitle(): string
    {
        return 'PHPMD Baseline Entry Count';
    }

    public function calculate(array $options): Value
    {
        if (! array_key_exists('baseline', $options) || ! is_string($options['baseline']) || $options['baseline'] === '') {
            $baseline = $options['baseline'] ?? '';

            throw InvalidOption::baselineOptionMustBeGiven((string) $baseline);
        }

        try {
            $baselineSet = BaselineSetFactory::fromFile($options['baseline']);

            /** @var array<string, ViolationBaseline[]> $violations */
            $violations = (new ReflectionProperty($baselineSet, 'violations'))->getValue($baselineSet);
        } catch (Throwable $e) {
            throw BaselineNotReadable::baselineLoadingFailed($options['baseline'], $e);
        }

        if (! array_key_exists('filter', $options) || ! is_array($options['filter'])) {
            return new Integer(array_sum(array_map('count', $violations)));
        }

        $filteredViolationsCount = 0;
        foreach ($violations as $ruleClass => $violationSet) {
            assert(class_exists($ruleClass));

            $rule = (new ReflectionClass($ruleClass))->getShortName();
            if (! in_array($rule, $options['filter'], true)) {
                continue;
            }

            $filteredViolationsCount += count($violationSet);
        }

        return new Integer($filteredViolationsCount);
    }
}
