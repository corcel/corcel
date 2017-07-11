<?php

namespace Corcel\Tests\Unit;

use Corcel\Model\Post;

/**
 * Class PostTypeTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class PostTypeTest extends \Corcel\Tests\TestCase
{
    /**
     * @test
     */
    public function it_still_has_post_type()
    {
        /** @var Post $post */
        $post = factory(Post::class)->create([
            'post_type' => 'video',
        ]);

        $this->assertInstanceOf(Post::class, $post);
    }

    /**
     * @test
     */
    public function it_has_custom_instance_name()
    {
        Post::registerPostType('video', Video::class);

        $post = factory(Post::class)->create(['post_type' => 'video']);
        $post = $post->fresh();

        $this->assertInstanceOf(Video::class, $post);
        $this->assertEquals('video', $post->getPostType());
    }
}

/**
 * Class Video
 */
class Video extends Post
{
    protected $postType = 'video';
}
