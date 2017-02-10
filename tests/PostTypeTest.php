<?php

use Corcel\Post;

class Video extends Post
{
}

class PostTypeTest extends PHPUnit_Framework_TestCase
{
    public function testPostTypeConstructor()
    {
        Post::registerPostType('video', 'Video');

        $post = new Post();
        $model = $post->newFromBuilder(['post_type' => 'video']);
        $this->assertInstanceOf('Video', $model);
    }
}
