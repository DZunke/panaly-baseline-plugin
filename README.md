# Panaly - Project Analyzer - PHPMD Plugin

The plugin to the [Panaly Project Analyzer](https://github.com/DZunke/panaly) delivers metrics analyzed
by [PHPMD](https://github.com/phpmd/phpmd) and to work with the baseline of the tool.

## Available Metrics

**Baseline Count**

The metric with the identifier `phpmd_baseline_count` gives an `Integer` result with the count baseline entries that can
be filtered by it's subject. The following options are available for the metric.

| Option   | Description                                                                                                              |
|----------|--------------------------------------------------------------------------------------------------------------------------|
| baseline | **(Required)** The baseline file of phpmd, it must be readable and containing the known phpmd xml format.                | 
| filter   | **(Optional)** An array of rules that the baseline should be filtered for, e.g. `StaticAccess` or `CyclomaticComplexity` |

## Example Configuration

```yaml
# panaly.dist.yaml
plugins:
    DZunke\PanalyPHPMD\PHPMDPlugin: ~ # no options available

groups:
    mass_detection:
        title: "Code Quality Metric"
        metrics:
            phpmd_baseline_count:
                baseline: ./path/to/my/baseline.xml
            phpmd_baseline_count_cyclomatic:
                title: PHPMD Cyclomatic Complexity Baseline Count
                baseline: ./path/to/my/baseline.xml
                filter: ['CyclomaticComplexity']
```

## Thanks and License

**Panaly Project Analyzer - PHPMD Plugin** © 2024+, Denis Zunke. Released utilizing
the [MIT License](https://mit-license.org/).

> GitHub [@dzunke](https://github.com/DZunke) &nbsp;&middot;&nbsp;
> Twitter [@DZunke](https://twitter.com/DZunke)
