<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Row\Entry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;
use Flow\ETL\Transformer\CaseConverter\CaseStyles;
use Jawira\CaseConverter\Convert;

/**
 * @psalm-immutable
 */
final class EntryNameCaseConverterTransformer implements Transformer
{
    private string $style;

    public function __construct(string $style)
    {
        /** @psalm-suppress ImpureFunctionCall */
        if (!\class_exists('Jawira\CaseConverter\Convert')) {
            throw new RuntimeException("Jawira\CaseConverter\Convert class not found, please add jawira/case-converter dependency to the project first.");
        }

        if (!\in_array($style, CaseStyles::ALL, true)) {
            throw new InvalidArgumentException("Unrecognized style {$style}, please use one of following: " . \implode(', ', CaseStyles::ALL));
        }

        $this->style = $style;
    }

    public function transform(Rows $rows) : Rows
    {
        /** @psalm-var pure-callable(Row $row) : Row $rowTransformer */
        $rowTransformer = function (Row $row) : Row {
            return $row->map(function (Entry $entry) : Entry {
                return $entry->rename(
                    /** @phpstan-ignore-next-line */
                    (string) \call_user_func([new Convert($entry->name()), 'to' . \ucfirst($this->style)])
                );
            });
        };

        return $rows->map($rowTransformer);
    }
}
