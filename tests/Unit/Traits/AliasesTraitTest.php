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
    /**
     * @test
     */
    public function it_inherits_aliases_from_parent()
    {
        $attachment = factory(Attachment::class)->create([
            'post_status' => 'foo',
            'post_content' => 'bar',
        ]);

        $this->assertNotNull($attachment->status);
        $this->assertNotNull($attachment->content);
        $this->assertNull($attachment->wrong_property);
    }

    /**
     * @test
     */
    public function it_has_aliases_after_to_array()
    {
        $post = factory(Post::class)->create([
            'post_title' => 'Test title',
        ]);
        $array = $post->toArray();

        // default accessor is working
        $this->assertEquals('Uncategorized', $array['main_category']);
        // default db value is working
        $this->assertEquals('Test title', $array['post_title']);
        // alias is in array
        $this->assertArrayHasKey('title', $array);
        // unknown keys are not in array
        $this->assertArrayNotHasKey('wrong_key', $array);
        // alias and db value are the same
        $this->assertEquals($array['post_title'], $array['title']);
    }
}
