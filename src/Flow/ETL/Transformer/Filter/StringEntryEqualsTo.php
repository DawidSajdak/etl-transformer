<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Filter;

use Flow\ETL\Row;

/**
 * @psalm-immutable
 */
final class StringEntryEqualsTo implements Filter
{
    private string $entryName;

    private string $entryValue;

    public function __construct(string $entryName, string $entryValue)
    {
        $this->entryName = $entryName;
        $this->entryValue = $entryValue;
    }

    public function __invoke(Row $row) : bool
    {
        return $row->valueOf($this->entryName) === $this->entryValue;
    }
}
