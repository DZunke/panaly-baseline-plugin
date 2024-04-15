<?php

declare(strict_types=1);

namespace DZunke\PanalyPHPMD\Test\Metric;

use DZunke\PanalyPHPMD\Metric\Baseline;
use DZunke\PanalyPHPMD\Metric\Exception\BaselineNotReadable;
use DZunke\PanalyPHPMD\Metric\Exception\InvalidOption;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BaselineTest extends TestCase
{
    public function testThatTheIdentifierIsCorrect(): void
    {
        self::assertSame(
            'phpmd_baseline_count',
            (new Baseline())->getIdentifier(),
        );
    }

    public function testThatTheDefaultTitleIsCorrect(): void
    {
        self::assertSame(
            'PHPMD Baseline Entry Count',
            (new Baseline())->getDefaultTitle(),
        );
    }

    public function testCalculationWithExistingBaseline(): void
    {
        $result = (new Baseline())->calculate(['baseline' => __DIR__ . '/../Fixtures/phpmdbaseline.xml']);

        self::assertSame(3, $result->compute());
    }

    public function testCalculationWithExistingBaselineFilteredByRule(): void
    {
        $result = (new Baseline())->calculate(
            [
                'baseline' => __DIR__ . '/../Fixtures/phpmdbaseline.xml',
                'filter' => ['StaticAccess'],
            ],
        );

        self::assertSame(2, $result->compute());
    }

    #[DataProvider('provideInvalidBaselineOptions')]
    public function testCalculationWithInvalidBaselineOption(array $options): void
    {
        $this->expectException(InvalidOption::class);
        $this->expectExceptionMessage('The baseline option must be a valid non-empty string.');

        (new Baseline())->calculate($options);
    }

    public static function provideInvalidBaselineOptions(): Generator
    {
        yield 'non existing baseline option' => [[]];
        yield 'empty baseline option' => [['baseline' => '']];
        yield 'non string baseline option' => [['baseline' => 12]];
    }

    public function testUnreadableBaselineThroesException(): void
    {
        $this->expectException(BaselineNotReadable::class);

        (new Baseline())->calculate(['baseline' => 'foo']);
    }
}
