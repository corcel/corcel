<?php

use Corcel\Post;
use Corcel\Page;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

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
        $this->assertInstanceOf("Video", $model);
    }
}
