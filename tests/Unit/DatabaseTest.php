<?php

namespace Corcel\Tests\Unit;

use Corcel\Model\Post;

/**
 * Class DatabaseTest
 *
 * @package Corcel\Tests\Unit
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class DatabaseTest extends \Corcel\Tests\TestCase
{
    public function test_it_uses_the_default_database_connection()
    {
        factory(Post::class)->create();

        $connection = config('database.default');
        $post = Post::newest()->first();

        $this->assertEquals($connection, $post->getConnectionName());
    }

    public function test_it_uses_corcel_connection_if_it_is_present()
    {
        factory(Post::class)->create();
        $post = Post::newest()->first();
        $this->assertInstanceOf(Post::class, $post);

        $this->app['config']->set('corcel.connection', 'foo');

        $post = Post::newest()->first();
        $this->assertNull($post);

        $post = factory(Post::class)->create();
        $this->assertEquals('foo', $post->getConnectionName());
    }
}
