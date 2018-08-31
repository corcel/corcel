<?php

namespace Corcel\Tests\Unit\Model\Builder;

use Corcel\Model\Post;
use Corcel\Tests\TestCase;

/**
 * Class PostBuilderTest
 *
 * @package Corcel\Tests\Unit\Model\Builder
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class PostBuilderTest extends TestCase
{
    /** @test */
    public function it_overrides_the_post_type_filter_if_it_is_called_more_than_once()
    {
        factory(Post::class, 2)->create(['post_type' => 'post']);
        factory(Post::class, 3)->create(['post_type' => 'page']);

        $query = Post::type('post')->type('page')->getQuery();
        $this->assertCount(1, collect($query->wheres)->where('column', '=', 'post_type'));
    }
}
