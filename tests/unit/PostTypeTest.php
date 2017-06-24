<?php

use Corcel\Post;

/**
 * Class PostTypeTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class PostTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function custom_post_still_has_post_type()
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
    public function custom_post_has_custom_instance_name()
    {
        Post::registerPostType('video', Video::class);

        $post = factory(Post::class)->create(['post_type' => 'video']);
        /** @var Post $post */
        $post = Post::find($post->ID);
        dd($post->toArray()); // FIXME

        $this->assertInstanceOf(Video::class, $post);
        $this->assertEquals('video', $post->getPostType()); // FIX
    }
}

/**
 * Class Video
 */
class Video extends Post
{
}
