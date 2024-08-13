<?php

declare(strict_types=1);

namespace DZunke\PanalyBaseline\Test\Metric;

use DZunke\PanalyBaseline\Metric\Exception\BaselineNotReadable;
use DZunke\PanalyBaseline\Metric\Exception\InvalidOption;
use DZunke\PanalyBaseline\Metric\PHPStanBaselineCount;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PHPStanBaselineCountTest extends TestCase
{
    public function testThatTheIdentifierIsCorrect(): void
    {
        self::assertSame(
            'phpstan_baseline_count',
            (new PHPStanBaselineCount())->getIdentifier(),
        );
    }

    public function testThatTheDefaultTitleIsCorrect(): void
    {
        self::assertSame(
            'PHPStan Baseline Count',
            (new PHPStanBaselineCount())->getDefaultTitle(),
        );
    }

    public function testCalculationWithExistingBaseline(): void
    {
        $result = (new PHPStanBaselineCount())->calculate(['baseline' => __DIR__ . '/../Fixtures/phpstanbaseline.neon']);

        self::assertSame(5, $result->getRaw());
    }

    /** @param list<string> $paths */
    #[DataProvider('providePathPatterns')]
    public function testCalculationWithExistingBaselineWithGivenPaths(array $paths, int $expectedResult): void
    {
        $result = (new PHPStanBaselineCount())->calculate([
            'baseline' => __DIR__ . '/../Fixtures/phpstanbaseline.neon',
            'paths' => $paths,
        ]);

        self::assertSame($expectedResult, $result->getRaw());
    }

    public static function providePathPatterns(): Generator
    {
        yield 'simple file' => [['src/Foo/Bar.php'], 2];
        yield 'star pattern' => [['src/Foo/*'], 5];
        yield 'recursive pattern' => [['src/**/*'], 5];
        yield 'all files named' => [['src/Foo/Bar.php', 'src/Foo/Baz.php'], 5];
        yield 'empty result after filtering' => [['src/Foo/Bar.js'], 0];
    }

    /** @param array<string, mixed> $options */
    #[DataProvider('provideInvalidBaselineOptions')]
    public function testCalculationWithInvalidBaselineOption(array $options): void
    {
        $this->expectException(InvalidOption::class);
        $this->expectExceptionMessage('The baseline option must be a valid non-empty string.');

        (new PHPStanBaselineCount())->calculate($options);
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

        (new PHPStanBaselineCount())->calculate(['baseline' => 'foo']);
    }
}
