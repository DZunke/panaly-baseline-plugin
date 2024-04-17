<?php

declare(strict_types=1);

namespace DZunke\PanalyBaseline\Psalm;

use DOMDocument;
use DOMElement;
use RuntimeException;

use function assert;
use function str_replace;

use const LIBXML_NOBLANKS;

/**
 * Hardly inspired by https://github.com/vimeo/psalm/blob/5.x/src/Psalm/ErrorBaseline.php but shrunk to the need
 * of this library as we just need file, error type and count. The occurred code piece is irrelevant.
 */
class BaselineReader
{
    private function __construct()
    {
    }

    /** @return array<string, array<string, int>> */
    public static function read(string $baselineContent): array
    {
        $baselineDoc = new DOMDocument();
        $baselineDoc->loadXML($baselineContent, LIBXML_NOBLANKS);

        $filesElement = $baselineDoc->getElementsByTagName('files');

        if ($filesElement->length === 0) {
            throw new RuntimeException('Baseline file does not contain <files>');
        }

        $files = [];

        $filesElement = $filesElement[0];
        assert($filesElement instanceof DOMElement);

        foreach ($filesElement->getElementsByTagName('file') as $file) {
            $fileName = $file->getAttribute('src');

            $fileName = str_replace('\\', '/', $fileName);

            $files[$fileName] = [];

            foreach ($file->childNodes as $issue) {
                if (! $issue instanceof DOMElement) {
                    continue;
                }

                $issueType = $issue->tagName;

                $files[$fileName][$issueType] = 0;
                $codeSamples                  = $issue->getElementsByTagName('code');

                foreach ($codeSamples as $codeSample) {
                    ++$files[$fileName][$issueType];
                }

                // For BC breaks left here - is not supported anymore with Psalm 6
                $occurrencesAttr = $issue->getAttribute('occurrences');
                if ($occurrencesAttr === '') {
                    continue;
                }

                $files[$fileName][$issueType] = (int) $occurrencesAttr;
            }
        }

        return $files;
    }
}
