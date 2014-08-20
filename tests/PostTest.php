<?php

use Corcel\Post;
use Corcel\Page;

class PostTest extends PHPUnit_Framework_TestCase
{
    public function testPostConstructor()
    {
        $post = new Post;
        $this->assertTrue($post instanceof \Corcel\Post);
    }

    public function testPostId()
    {
        $post = Post::find(1);

        if ($post) {
            $this->assertEquals($post->ID, 1);
        } else {
            $this->assertEquals($post, null);
        }
    }

    public function testPostType()
    {
        $post = Post::type('page')->first();
        $this->assertEquals($post->post_type, 'page');

        $page = Page::first();
        $this->assertEquals($page->post_type, 'page');
    }

    public function testPostCustomFields()
    {
        $post = Post::find(2);
        $this->assertNotEmpty($post->meta);
        $this->assertNotEmpty($post->fields);

        $this->assertTrue($post->meta instanceof \Corcel\PostMetaCollection);
    }

    public function testTaxonomies()
    {
        $post = Post::find(1);
        $taxonomy = $post->taxonomies()->first();
        $this->assertEquals($taxonomy->taxonomy, 'category');

        $post = Post::taxonomy('category', 'php')->first();
        $this->assertEquals($post->ID, 1);

        $post = Post::taxonomy('category', 'php')->first();
        $this->assertEquals($post->post_type, 'post');
    }

    public function testUpdateCustomFields()
    {
        $post = Post::find(1);
        $post->meta->username = 'juniorgrossi';
        $post->meta->url = 'http://grossi.io';
        $post->save();

        $post = Post::find(1);
        $this->assertEquals($post->meta->username, 'juniorgrossi');
        $this->assertEquals($post->meta->url, 'http://grossi.io');
    }

    public function testInsertCustomFields()
    {
        $post = new Post;
        $post->save();

        $post->meta->username = 'juniorgrossi';
        $post->meta->url = 'http://grossi.io';
        $post->save();

        $post = Post::find($post->ID);
        $this->assertEquals($post->meta->username, 'juniorgrossi');
        $this->assertEquals($post->meta->url, 'http://grossi.io');
    }
}