<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Unit;

use Flow\ETL\Row;
use Flow\ETL\Row\Entry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\MathOperationTransformer;
use PHPUnit\Framework\TestCase;

final class MathOperationTransformerTest extends TestCase
{
    /**
     * @dataProvider math_operations_provider
     */
    public function test_math_operations(Entry $leftEntry, Entry $rightEntry, string $operation, $result, $resultClass) : void
    {
        $rows = MathOperationTransformer::$operation($leftEntry->name(), $rightEntry->name())
            ->transform(new Rows(Row::create($leftEntry, $rightEntry)));

        $this->assertSame(
            [
                [
                    'left' => $leftEntry->value(),
                    'right' => $rightEntry->value(),
                    $operation => $result,
                ],
            ],
            $rows->toArray()
        );
        $this->assertInstanceOf(
            $resultClass,
            $rows->first()->get($operation)
        );
    }

    public function math_operations_provider() : \Generator
    {
        yield [new Entry\IntegerEntry('left', 10), new Entry\IntegerEntry('right', 10), 'add', 20, Entry\IntegerEntry::class];
        yield [new Entry\IntegerEntry('left', 10), new Entry\IntegerEntry('right', 10), 'subtract', 0, Entry\IntegerEntry::class];
        yield [new Entry\IntegerEntry('left', 10), new Entry\IntegerEntry('right', 5), 'divide', 2, Entry\IntegerEntry::class];
        yield [new Entry\IntegerEntry('left', 10), new Entry\IntegerEntry('right', 5), 'multiply', 50, Entry\IntegerEntry::class];
        yield [new Entry\IntegerEntry('left', 2), new Entry\IntegerEntry('right', 3), 'power', 8, Entry\IntegerEntry::class];
        yield [new Entry\IntegerEntry('left', 5), new Entry\IntegerEntry('right', 2), 'divide', 2.5, Entry\FloatEntry::class];
    }
}
