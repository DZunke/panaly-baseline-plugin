<?php

declare(strict_types=1);

namespace DZunke\PanalyBaseline;

use DZunke\PanalyBaseline\Metric\PHPMDBaselineCount;
use DZunke\PanalyBaseline\Metric\PHPStanBaselineCount;
use DZunke\PanalyBaseline\Metric\PsalmBaselineCount;
use Panaly\Plugin\BasePlugin;

final class BaselinePlugin extends BasePlugin
{
    /** @inheritDoc */
    public function getAvailableMetrics(array $options): array
    {
        return [
            new PHPMDBaselineCount(),
            new PsalmBaselineCount(),
            new PHPStanBaselineCount(),
        ];
    }
}
