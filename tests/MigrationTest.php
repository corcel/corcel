<?php

use Corcel\Post;

/**
 * Class MigrationTest
 */
class MigrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function schema_is_working_in_memory()
    {
        $post = \factory(Post::class)->create();

        $this->assertTrue(is_string($post->post_content));
        $this->assertInstanceOf(Post::class, $post);
    }
}
