<?php

namespace Corcel\Tests\Unit\Model\Meta;

use Corcel\Model\Meta\PostMeta;
use Corcel\Tests\TestCase;

class MetaTest extends TestCase
{
    public function test_it_might_not_serialize_value()
    {
        // meta_value is not serialized
        $meta = factory(PostMeta::class)->create(['meta_value' => 'foo']);
        $this->assertEquals('foo', $meta->value);
    }    
}
