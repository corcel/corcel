<?php

namespace Corcel\Tests\Unit\Traits;

use Corcel\Model;
use Corcel\Model\Post;
use Corcel\Model\User;
use Illuminate\Support\Collection;

/**
 * Class HasMetaFieldsTest
 *
 * @package Corcel\Tests\Unit\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class HasMetaFieldsTest extends \Corcel\Tests\TestCase
{
    /**
     * @test
     */
    public function it_can_update_meta()
    {
        $post = factory(Post::class)->create();
        $post->saveMeta('foo', 'bar');
        $post->saveMeta('foo', 'baz');

        $meta = $post->meta()->where('meta_key', 'foo')->first();

        $this->assertEquals('baz', $meta->meta_value);
    }

    /**
     * @test
     */
    public function it_can_save_multiples_metas()
    {
        $user = factory(User::class)->create();

        $user->saveMeta([
            'foo' => 'bar',
            'fee' => 'baz',
        ]);

        $this->assertEquals('bar', $user->meta->foo);
        $this->assertEquals('baz', $user->meta->fee);
    }

    /**
     * @test
     */
    public function it_can_create_multiples_metas()
    {
        $user = factory(User::class)->create();

        $user->createMeta([
            'foo' => 'bar',
            'fee' => 'baz',
        ]);

        $this->assertEquals('bar', $user->meta->foo);
        $this->assertEquals('baz', $user->meta->fee);
        $this->assertEquals(2, $user->meta->count());
    }

    /**
     * @test
     */
    public function it_gets_meta_after_creating_meta()
    {
        $user = factory(User::class)->create();

        $metas = $user->createMeta(['foo' => 'bar']);
        $this->assertInstanceOf(Collection::class, $metas);
        $this->assertEquals(1, $metas->count());

        $meta = $user->createMeta('foo', 'bar');
        $this->assertInstanceOf(Model::class, $meta);
    }

    /**
     * @test
     */
    public function it_can_get_meta_data_from_get_meta_method()
    {
        $user = factory(User::class)->create();

        $user->createMeta('foo', 'bar');

        $this->assertEquals('bar', $user->getMeta('foo'));
    }
}
