<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Unit;

use Flow\ETL\Row;
use Flow\ETL\Row\Entry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\CallbackEntryTransformer;
use PHPUnit\Framework\TestCase;

class CallbackEntryTransformerTest extends TestCase
{
    public function test_replacing_dashes_in_entry_name_with_str_replace_callback() : void
    {
        $callbackTransformer = new CallbackEntryTransformer(
            fn (Entry $entry) : Entry => new $entry(\str_replace('-', '_', $entry->name()), $entry->value())
        );

        $rows = $callbackTransformer->transform(
            new Rows(
                Row::create(
                    new Row\Entry\IntegerEntry('old-int', 1000),
                    new Entry\StringEntry('string-entry ', 'String entry')
                )
            )
        );

        $this->assertEquals(new Rows(
            Row::create(
                new Row\Entry\IntegerEntry('old_int', 1000),
                new Entry\StringEntry('string_entry ', 'String entry')
            )
        ), $rows);
    }

    public function test_removing_whitespace_with_trim_callback() : void
    {
        $callbackTransformer = new CallbackEntryTransformer(
            fn (Entry $entry) : Entry => new $entry(\trim($entry->name()), $entry->value())
        );

        $rows = $callbackTransformer->transform(
            new Rows(
                Row::create(
                    new Entry\StringEntry('string entry ', 'String entry')
                )
            )
        );

        $this->assertEquals(new Rows(
            Row::create(
                new Entry\StringEntry('string entry', 'String entry')
            )
        ), $rows);
    }
}