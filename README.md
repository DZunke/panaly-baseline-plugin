# Panaly - Project Analyzer - Baseline Plugin

The plugin to the [Panaly Project Analyzer](https://github.com/DZunke/panaly) delivers metrics analyzed for different
tool baselines from the
development ecosystem. If one introduces a new quality tool, new quality rules or is in a haste it is not always
possible to have all errors fixed that quality tools are delivering. So most tooling is delivering a feature that is
named "baseline" to store all existing errors to a specific time which will then be ignored for the checks. So
developers get the time to fix those but can deliver new stuff with the quality tools enabled - so no new tech debt.

To have an overview about the baselines this plugin delivers some metrics that can analyze those baselines to have an
idea of the tech debt that was left behind the development and to see how the tech debt developers as there could
also be reasons to put additional stuff to the baselines.

## Available Metrics

**PHPMD Baseline Count**

The metric with the identifier `phpmd_baseline_count` gives an `Integer` result with the count entries of a
phpmd baseline file that can be filtered by its subject. The following options are available for the metric.

| Option   | Description                                                                                                              |
|----------|--------------------------------------------------------------------------------------------------------------------------|
| baseline | **(Required)** The baseline file of phpmd, it must be readable and containing the known phpmd xml format.                | 
| filter   | **(Optional)** An array of rules that the baseline should be filtered for, e.g. `StaticAccess` or `CyclomaticComplexity` |

**PHPStan Baseline Count**

The metric with the identifier `phpstan_baseline_count` gives an `Integer` result which is containing the sum of entries
of a phpstan baseline file. It sums not only the entries that are in there but summarizes the count of each entry
as identical messages from a single file are put together with a count within the file.

| Option   | Description                                                                                                                   |
|----------|-------------------------------------------------------------------------------------------------------------------------------|
| baseline | **(Required)** The baseline file of phpstan, it must be readable and containing the known phpstan neon format.                | 
| paths    | **(Optional)** Only count files for specific paths from the baseline file. A path could be anything valid in Codeowners file. | 

**Psalm Baseline Count**

The metric with the identifier `psalm_baseline_count` gives an `Integer` result which contains the sum of all `code`
entries which represent a single error within a class.

| Option   | Description                                                                                               |
|----------|-----------------------------------------------------------------------------------------------------------|
| baseline | **(Required)** The baseline file of psalm, it must be readable and containing the known psalm xml format. | 

## Example Configuration

```yaml
# panaly.dist.yaml
plugins:
    DZunke\PanalyBaseline\BaselinePlugin: ~ # no options available

groups:
    baselines:
        title: "Baseline Overview"
        metrics:
            phpmd_baseline_count:
                baseline: ./path/to/my/baseline.xml
            phpmd_baseline_count_cyclomatic:
                title: PHPMD Cyclomatic Complexity Baseline Count
                baseline: ./path/to/my/baseline.xml
                filter: [ 'CyclomaticComplexity' ]
```

## Thanks and License

**Panaly Project Analyzer - Baseline Plugin** © 2024+, Denis Zunke. Released utilizing
the [MIT License](https://mit-license.org/).

> GitHub [@dzunke](https://github.com/DZunke) &nbsp;&middot;&nbsp;
> Twitter [@DZunke](https://twitter.com/DZunke)
