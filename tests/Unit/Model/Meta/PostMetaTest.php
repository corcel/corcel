<?php

namespace Corcel\Tests\Unit\Model\Meta;

use Corcel\Model\Meta\PostMeta;
use Corcel\Model\Post;

/**
 * Class PostMetaTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class PostMetaTest extends \Corcel\Tests\TestCase
{
    /**
     * @test
     */
    public function it_has_correct_instance_type()
    {
        $meta = factory(PostMeta::class)->create();

        $this->assertInstanceOf(PostMeta::class, $meta);
    }

    /**
     * @test
     */
    public function its_id_is_an_integer()
    {
        $meta = factory(PostMeta::class)->create();

        $this->assertNotNull($meta);
        $this->assertTrue(is_int($meta->meta_id));
    }

    /**
     * @test
     */
    public function it_has_post_relation()
    {
        $meta = $this->createMetaWithPost();

        $this->assertInstanceOf(Post::class, $meta->post);
    }

    /**
     * @test
     */
    public function it_has_meta_key_and_value()
    {
        $meta = factory(PostMeta::class)->create();

        $this->assertNotNull($meta);
        $this->assertNotNull($meta->meta_key);
        $this->assertNotNull($meta->meta_value);
    }

    /**
     * @test
     */
    public function its_value_has_the_same_value_than_post_meta_value()
    {
        $meta = $this->createMetaWithPost();

        $post = $meta->post;
        $key = $meta->meta_key;

        $this->assertEquals($meta->meta_value, $post->meta->$key);
    }

    /**
     * @test
     */
    public function its_value_can_be_reached_by_value_property()
    {
        $meta = factory(PostMeta::class)->create();

        $this->assertNotNull($meta->value);
        $this->assertEquals($meta->meta_value, $meta->value);
    }

    /**
     * @test
     */
    public function its_value_can_be_serialized()
    {
        $meta = factory(PostMeta::class)->create();

        $meta->meta_value = serialize($expected = ['foo' => 'bar']);

        $this->assertEquals($expected, $meta->value);
    }

    /**
     * @test
     */
    public function it_has_has_meta_scope()
    {
        $post = factory(Post::class)->create();
        $post->saveMeta('one', 'two');
        $post->saveMeta('three', 'four');

        $newPost = Post::hasMeta('one')->first();
        $this->assertEquals($post->ID, $newPost->ID);

        $newPost = Post::hasMeta('one', 'two')->first();
        $this->assertEquals($post->ID, $newPost->ID);
    }

    /**
     * @test
     */
    public function its_has_meta_scope_accepts_array_as_parameter()
    {
        $post = factory(Post::class)->create();
        $post->saveMeta('one', 'two');
        $post->saveMeta('three', 'four');

        $newPost = Post::hasMeta(['one' => 'two'])->first();

        $this->assertNotNull($newPost);
        $this->assertEquals($post->title, $newPost->title);
        $this->assertEquals($post->ID, $newPost->ID);

        $newPost = Post::hasMeta([
            'one' => 'two',
            'three' => 'four',
        ])->first();

        $this->assertNotNull($newPost);
        $this->assertEquals($post->title, $newPost->title);
        $this->assertEquals($post->ID, $newPost->ID);
    }

    /**
     * @test
     */
    public function its_has_meta_scope_can_have_array_with_only_values()
    {
        $post = factory(Post::class)->create();
        $post->saveMeta('one', 'two');
        $post->saveMeta('three', 'four');

        $newPost = Post::hasMeta(['one', 'three'])->first();


        $this->assertNotNull($newPost);
        $this->assertEquals($post->title, $newPost->title);
        $this->assertEquals($post->ID, $newPost->ID);
    }

    /**
     * @return PostMeta
     */
    private function createMetaWithPost()
    {
        return factory(PostMeta::class)->create([
            'post_id' => function () {
                return factory(Post::class)->create()->ID;
            },
        ]);
    }
}
