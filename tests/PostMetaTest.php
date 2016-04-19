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

    public function testPostMetaValue()
    {
        //test value when meta_value is string
        $metaWithString = PostMeta::find(1);
        $stringValue = '2016-04-03';
        $metaWithString->meta_value = $stringValue;
        $this->assertEquals($stringValue, $metaWithString->value);

        //test value when meta_value is serialized array
        $metaWithArray = PostMeta::find(1);
        $arrayValue = ['key' => 'value'];
        $metaWithArray->meta_value = serialize($arrayValue);
        $this->assertEquals($arrayValue, $metaWithArray->value);
    }

    public function testSerializedData()
    {
        $post = Post::find(45);
        $this->assertTrue(is_array($post->meta->username));
    }
}