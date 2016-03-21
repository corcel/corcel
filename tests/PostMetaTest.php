<?php

use Corcel\Post;
use Corcel\Page;
use Corcel\PostMeta;

class PostMetaTest extends PHPUnit_Framework_TestCase
{
    public function testPostMetaConstructor()
    {
        $postmeta = new PostMeta();
        $this->assertTrue($postmeta instanceof \Corcel\PostMeta);
    }

    public function testPostId()
    {
        $postmeta = PostMeta::find(1);

        if ($postmeta) {
            $this->assertEquals($postmeta->meta_id, 1);
        } else {
            $this->assertEquals($postmeta, null);
        }
    }

    public function testPostRelation()
    {
        $postmeta = PostMeta::find(1);
        $this->assertTrue($postmeta->post instanceof \Corcel\Post);
    }
}