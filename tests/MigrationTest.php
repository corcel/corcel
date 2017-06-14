<?php

use Corcel\Post;

/**
 * Class MigrationTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class MigrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function database_factory_is_working()
    {
        $post = factory(Post::class)->create();

        $this->assertTrue(is_string($post->post_content));
        $this->assertInstanceOf(Post::class, $post);
    }
}
