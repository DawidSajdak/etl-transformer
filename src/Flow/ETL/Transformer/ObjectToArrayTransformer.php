<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;
use Laminas\Hydrator\HydratorInterface;

/**
 * @psalm-immutable
 */
final class ObjectToArrayTransformer implements Transformer
{
    private string $objectEntryName;

    private HydratorInterface $hydrator;

    public function __construct(HydratorInterface $hydrator, string $objectEntryName)
    {
        $this->objectEntryName = $objectEntryName;
        $this->hydrator = $hydrator;
    }

    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress MixedArgument
     */
    public function transform(Rows $rows) : Rows
    {
        return $rows->map(function (Row $row) : Row {
            if (!$row->entries()->has($this->objectEntryName)) {
                throw new RuntimeException("\"{$this->objectEntryName}\" not found");
            }

            if (!$row->entries()->get($this->objectEntryName) instanceof Row\Entry\ObjectEntry) {
                throw new RuntimeException("\"{$this->objectEntryName}\" is not ObjectEntry");
            }

            $entries = $row->entries()
                ->remove($this->objectEntryName)
                ->add(
                    new Row\Entry\ArrayEntry(
                        $this->objectEntryName,
                        $this->hydrator->extract(
                            $row->valueOf($this->objectEntryName)
                        )
                    )
                );

            return new Row($entries);
        });
    }
}
