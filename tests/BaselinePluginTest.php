<?php

declare(strict_types=1);

namespace DZunke\PanalyBaseline\Test;

use DZunke\PanalyBaseline\BaselinePlugin;
use DZunke\PanalyBaseline\Metric\PHPMDBaselineCount;
use DZunke\PanalyBaseline\Metric\PHPStanBaselineCount;
use DZunke\PanalyBaseline\Metric\PsalmBaselineCount;
use Panaly\Configuration\ConfigurationFile;
use Panaly\Configuration\RuntimeConfiguration;
use PHPUnit\Framework\TestCase;

class BaselinePluginTest extends TestCase
{
    public function testPluginInitialization(): void
    {
        self::assertInstanceOf(BaselinePlugin::class, new BaselinePlugin());
    }

    public function testPluginInitializesMetrics(): void
    {
        $configurationFile    = $this->createMock(ConfigurationFile::class);
        $runtimeConfiguration = $this->createMock(RuntimeConfiguration::class);

        $matcher = $this->exactly(3);

        $runtimeConfiguration->expects($matcher)
            ->method('addMetric')
            ->willReturnCallback(static function (object $metric) use ($matcher): void {
                match ($matcher->numberOfInvocations()) {
                    1 => self::assertInstanceOf(PHPMDBaselineCount::class, $metric),
                    2 => self::assertInstanceOf(PsalmBaselineCount::class, $metric),
                    3 => self::assertInstanceOf(PHPStanBaselineCount::class, $metric),
                    default => self::fail('Too much is going on here!'),
                };
            });

        (new BaselinePlugin())->initialize($configurationFile, $runtimeConfiguration, []);
    }
}
