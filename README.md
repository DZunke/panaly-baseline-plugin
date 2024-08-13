# Panaly - Project Analyzer - Baseline Plugin

The plugin for the [Panaly Project Analyzer](https://github.com/DZunke/panaly) provides metrics for various tool
baselines in the development ecosystem. When introducing new quality tools or rules, it's not always feasible to fix all
errors immediately. Most tools offer a "baseline" feature to store existing errors at a specific time, which are then
ignored during checks. This allows developers to fix these issues over time while ensuring new code meets quality
standards, preventing new technical debt.

## Available Metrics

### PHPMD Baseline Count

Identifier: `phpmd_baseline_count`

Returns an `Integer` result with the count of entries in a PHPMD baseline file, which can be filtered by subject.

| Option   | Description                                                                                              |
|----------|----------------------------------------------------------------------------------------------------------|
| baseline | **(Required)** The PHPMD baseline file, which must be readable and in the known PHPMD XML format.        |
| filter   | **(Optional)** An array of rules to filter the baseline, e.g., `StaticAccess` or `CyclomaticComplexity`. |

### PHPStan Baseline Count

Identifier: `phpstan_baseline_count`

Returns an `Integer` result with the sum of entries in a PHPStan baseline file, summarizing the count of each entry.

| Option   | Description                                                                                                                 |
|----------|-----------------------------------------------------------------------------------------------------------------------------|
| baseline | **(Required)** The PHPStan baseline file, which must be readable and in the known PHPStan NEON format.                      |
| paths    | **(Optional)** Only count files for specific paths in the baseline file. A path can be anything valid in a CODEOWNERS file. |

### Psalm Baseline Count

Identifier: `psalm_baseline_count`

Returns an `Integer` result with the sum of all `code` entries representing a single error within a class.

| Option   | Description                                                                                                                 |
|----------|-----------------------------------------------------------------------------------------------------------------------------|
| baseline | **(Required)** The Psalm baseline file, which must be readable and in the known Psalm XML format.                           |
| paths    | **(Optional)** Only count files for specific paths in the baseline file. A path can be anything valid in a CODEOWNERS file. |

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
