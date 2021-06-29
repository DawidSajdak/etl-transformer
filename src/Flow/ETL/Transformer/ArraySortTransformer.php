<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class ArraySortTransformer implements Transformer
{
    private string $arrayEntryName;

    private int $sortingFlag;

    public function __construct(string $arrayEntry, int $sortingFlag = \SORT_REGULAR)
    {
        $this->arrayEntryName = $arrayEntry;
        $this->sortingFlag = $sortingFlag;
    }

    public function transform(Rows $rows) : Rows
    {
        /**
         * @psalm-var pure-callable(Row $row) : Row $transformer
         */
        $transformer = function (Row $row) : Row {
            if (!$row->entries()->has($this->arrayEntryName)) {
                throw new RuntimeException("\"{$this->arrayEntryName}\" not found");
            }

            if (!$row->entries()->get($this->arrayEntryName) instanceof Row\Entry\ArrayEntry) {
                throw new RuntimeException("\"{$this->arrayEntryName}\" is not ArrayEntry");
            }

            $arrayEntry = $row->get($this->arrayEntryName);

            /** @var array<mixed> $array */
            $array = $arrayEntry->value();
            \sort($array, $this->sortingFlag);

            return $row->remove($arrayEntry->name())
                ->add(new Row\Entry\ArrayEntry(
                    $arrayEntry->name(),
                    $array
                ));
        };

        return $rows->map($transformer);
    }
}
