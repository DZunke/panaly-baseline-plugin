<?php

declare(strict_types=1);

namespace DZunke\PanalyBaseline\Test\Metric;

use DZunke\PanalyBaseline\Metric\Exception\InvalidOption;
use DZunke\PanalyBaseline\Metric\PsalmBaselineCount;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PsalmBaselineCountTest extends TestCase
{
    public function testThatTheIdentifierIsCorrect(): void
    {
        self::assertSame(
            'psalm_baseline_count',
            (new PsalmBaselineCount())->getIdentifier(),
        );
    }

    public function testThatTheDefaultTitleIsCorrect(): void
    {
        self::assertSame(
            'Psalm Baseline Count',
            (new PsalmBaselineCount())->getDefaultTitle(),
        );
    }

    public function testCalculationWithExistingBaseline(): void
    {
        $result = (new PsalmBaselineCount())->calculate(['baseline' => __DIR__ . '/../Fixtures/psalm-baseline.xml']);

        self::assertSame(8, $result->compute());
    }

    #[DataProvider('provideInvalidBaselineOptions')]
    public function testCalculationWithInvalidBaselineOption(array $options): void
    {
        $this->expectException(InvalidOption::class);
        $this->expectExceptionMessage('The baseline option must be a valid non-empty string.');

        (new PsalmBaselineCount())->calculate($options);
    }

    public static function provideInvalidBaselineOptions(): Generator
    {
        yield 'non existing baseline option' => [[]];
        yield 'empty baseline option' => [['baseline' => '']];
        yield 'non string baseline option' => [['baseline' => 12]];
    }

    public function testUnreadableBaselineThroesException(): void
    {
        $this->expectException(InvalidOption::class);
        $this->expectExceptionMessage('The given baseline "foo" is not an existing file or not readable.');

        (new PsalmBaselineCount())->calculate(['baseline' => 'foo']);
    }
}
