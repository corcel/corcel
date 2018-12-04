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
    public function test_it_can_update_meta()
    {
        $post = factory(Post::class)->create();
        $post->saveMeta('foo', 'bar');
        $post->saveMeta('foo', 'baz');

        $meta = $post->meta()->where('meta_key', 'foo')->first();

        $this->assertEquals('baz', $meta->meta_value);
    }

    public function test_it_can_save_multiples_metas()
    {
        $user = factory(User::class)->create();

        $user->saveMeta([
            'foo' => 'bar',
            'fee' => 'baz',
        ]);

        $this->assertEquals('bar', $user->meta->foo);
        $this->assertEquals('baz', $user->meta->fee);
    }

    public function test_it_can_create_multiples_metas()
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

    public function test_it_gets_meta_after_creating_meta()
    {
        $user = factory(User::class)->create();

        $metas = $user->createMeta(['foo' => 'bar']);
        $this->assertInstanceOf(Collection::class, $metas);
        $this->assertEquals(1, $metas->count());

        $meta = $user->createMeta('foo', 'bar');
        $this->assertInstanceOf(Model::class, $meta);
    }

    public function test_it_can_get_meta_data_from_get_meta_method()
    {
        $user = factory(User::class)->create();

        $user->createMeta('foo', 'bar');

        $this->assertEquals('bar', $user->getMeta('foo'));
    }

    public function test_it_can_check_meta_using_has_meta_method()
    {
        factory(User::class)->create()->createMeta(['foo' => 'ba']);
        factory(User::class)->create()->createMeta(['foo' => 'bar']);
        factory(User::class)->create()->createMeta(['foo' => 'baz']);
        factory(User::class)->create()->createMeta(['foo' => 'BA']);

        /** @var Collection $users */
        $users = User::hasMeta(['foo' => 'ba'])->get();

        $this->assertInstanceOf(Collection::class, $users);
        $this->assertEquals(1, $users->count());
    }

    public function test_it_can_find_users_by_meta_like_after_creating_meta()
    {
        factory(User::class)->create()->createMeta(['foo' => 'ba']);
        factory(User::class)->create()->createMeta(['foo' => 'bar']);
        factory(User::class)->create()->createMeta(['foo' => 'baz']);
        factory(User::class)->create()->createMeta(['foo' => 'BA']);

        /** @var Collection $users */
        $users = User::hasMetaLike(['foo' => 'ba'])->get();

        $this->assertInstanceOf(Collection::class, $users);
        $this->assertEquals(2, $users->count());
    }

    public function test_it_can_find_users_by_meta_like_with_wildcard_after_creating_meta()
    {
        factory(User::class)->create()->createMeta(['foo' => 'ba']);
        factory(User::class)->create()->createMeta(['foo' => 'bar']);
        factory(User::class)->create()->createMeta(['foo' => 'baz']);

        /** @var Collection $users */
        $users = User::hasMetaLike(['foo' => 'ba%'])->get();

        $this->assertInstanceOf(Collection::class, $users);
        $this->assertEquals(3, $users->count());
    }
}
