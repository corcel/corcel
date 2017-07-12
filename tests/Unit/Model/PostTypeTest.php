<?php

namespace Corcel\Tests\Unit\Model;

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
        factory(Post::class)->create(['post_type' => 'video']);

        $post = Post::newest()->first();

        $this->assertInstanceOf(Video::class, $post);
        $this->assertEquals('video', $post->getPostType());
    }

    /**
     * @test
     */
    public function it_has_custom_instance_using_custom_class_builder()
    {
        Post::registerPostType('video', Video::class);
        factory(Post::class)->create(['post_type' => 'video']);

        $video = Video::first();

        $this->assertInstanceOf(Video::class, $video);
        $this->assertEquals('video', $video->post_type);
    }
}

/**
 * Class Video
 */
class Video extends Post
{
    protected $postType = 'video';
}
