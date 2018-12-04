<?php

namespace Corcel\Tests\Unit\Traits;

use Corcel\Model\Attachment;
use Corcel\Model\Post;

/**
 * Class AliasesTraitTest
 *
 * @package Corcel\Tests\Unit\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class AliasesTraitTest extends \Corcel\Tests\TestCase
{
    public function test_it_inherits_aliases_from_parent()
    {
        $attachment = factory(Attachment::class)->create([
            'post_status' => 'foo',
            'post_content' => 'bar',
        ]);

        $this->assertNotNull($attachment->status);
        $this->assertNotNull($attachment->content);
        $this->assertNull($attachment->wrong_property);
    }

    public function test_it_has_aliases_after_to_array()
    {
        $post = factory(Post::class)->create([
            'post_title' => 'Test title',
        ]);
        $array = $post->toArray();

        $this->assertEquals('Uncategorized', $array['main_category']);
        $this->assertEquals('Test title', $array['post_title']);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayNotHasKey('wrong_key', $array);
        $this->assertEquals($array['post_title'], $array['title']);
    }
}
