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
    public function test_it_still_has_post_type()
    {
        /** @var Post $post */
        $post = factory(Post::class)->create([
            'post_type' => 'video',
        ]);

        $this->assertInstanceOf(Post::class, $post);
    }

    public function test_it_has_custom_instance_name()
    {
        Post::registerPostType('video', Video::class);
        factory(Post::class)->create(['post_type' => 'video']);

        $post = Post::newest()->first();

        $this->assertInstanceOf(Video::class, $post);
        $this->assertEquals('video', $post->getPostType());
    }

    public function test_it_has_meta_fields_using_custom_class()
    {
        factory(Post::class)->create(['post_type' => 'fake_post']);
        $fake = Post::newest()->first();

        $this->assertInstanceOf(FakePost::class, $fake);

        $fake->createMeta('foo', 'bar');

        $this->assertEquals('bar', $fake->meta->foo);
    }

    public function test_it_has_custom_instance_using_custom_class_builder()
    {
        Post::registerPostType('video', Video::class);
        factory(Post::class)->create(['post_type' => 'video']);

        $video = Video::first();

        $this->assertInstanceOf(Video::class, $video);
        $this->assertEquals('video', $video->post_type);
    }

    public function test_it_is_configurable_by_the_config_file()
    {
        factory(Post::class)->create(['post_type' => 'fake_post']);
        $post = Post::type('fake_post')->first();
        $this->assertNotNull($post);
        $this->assertInstanceOf(FakePost::class, $post);

        factory(Post::class)->create(['post_type' => 'fake_page']);
        $post = Post::type('fake_page')->first();
        $this->assertNotNull($post);
        $this->assertInstanceOf(FakePage::class, $post);
    }
}

class Video extends Post
{
    protected $postType = 'video';
}

class FakePost extends Post
{
    protected $postType = 'fake_post';
}

class FakePage extends Post
{
    protected $postType = 'fake_page';
}
