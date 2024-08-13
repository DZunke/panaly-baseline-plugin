<?php

declare(strict_types=1);

namespace DZunke\PanalyBaseline;

use DZunke\PanalyBaseline\Metric\PHPMDBaselineCount;
use DZunke\PanalyBaseline\Metric\PHPStanBaselineCount;
use DZunke\PanalyBaseline\Metric\PsalmBaselineCount;
use Panaly\Configuration\ConfigurationFile;
use Panaly\Configuration\RuntimeConfiguration;
use Panaly\Plugin\Plugin;

final class BaselinePlugin implements Plugin
{
    public function initialize(
        ConfigurationFile $configurationFile,
        RuntimeConfiguration $runtimeConfiguration,
        array $options,
    ): void {
        $runtimeConfiguration->addMetric(new PHPMDBaselineCount());
        $runtimeConfiguration->addMetric(new PsalmBaselineCount());
        $runtimeConfiguration->addMetric(new PHPStanBaselineCount());
    }
}
