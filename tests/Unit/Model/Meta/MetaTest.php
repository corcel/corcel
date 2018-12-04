<?php

namespace Corcel\Tests\Unit\Model\Meta;

use Corcel\Model\Meta\PostMeta;
use Corcel\Tests\TestCase;

class MetaTest extends TestCase
{
    public function test_it_unserialize_serialized_values()
    {
        $meta = factory(PostMeta::class)->create(['meta_value' => serialize('foo')]);
        $this->assertEquals('foo', $meta->value);
    }

    public function test_it_also_works_with_unserialized_values()
    {
        $meta = factory(PostMeta::class)->create(['meta_value' => 'foo']);
        $this->assertEquals('foo', $meta->value);
    }
}
