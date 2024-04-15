<?php

declare(strict_types=1);

namespace DZunke\PanalyPHPMD;

use DZunke\PanalyPHPMD\Metric\Baseline;
use Panaly\Plugin\BasePlugin;

final class PHPMDPlugin extends BasePlugin
{
    /** @inheritDoc */
    public function getAvailableMetrics(array $options): array
    {
        return [new Baseline()];
    }
}
