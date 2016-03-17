<?php

use Corcel\Post;
use Corcel\Page;

class PostTest extends PHPUnit_Framework_TestCase
{
    public function testPostConstructor()
    {
        $post = new Post();
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

    /**
     * Tests the post accessors
     * Accessors should be equal to the original value.
     */
    public function testPostAccessors()
    {
        $post = Post::find(2);
        $this->assertEquals($post->post_title, $post->title);
        $this->assertEquals($post->post_name, $post->slug);
        $this->assertEquals($post->post_content, $post->content);
        $this->assertEquals($post->post_type, $post->type);
        $this->assertEquals($post->post_mime_type, $post->mime_type);
        $this->assertEquals($post->guid, $post->url);
        $this->assertEquals($post->post_author, $post->author_id);
        $this->assertEquals($post->post_parent, $post->parent_id);
        $this->assertEquals($post->post_date, $post->created_at);
        $this->assertEquals($post->post_modified, $post->updated_at);
        $this->assertEquals($post->post_excerpt, $post->exceprt);
        $this->assertEquals($post->post_status, $post->status);
    }

    public function testPostCustomFields()
    {
        $post = Post::find(2);
        $this->assertNotEmpty($post->meta);
        $this->assertNotEmpty($post->fields);

        $this->assertTrue($post->meta instanceof \Corcel\PostMetaCollection);
    }

    public function testPostOrderBy()
    {
        $posts = Post::orderBy('post_date', 'asc')->get();

        $lastDate = null;
        foreach ($posts as $post) {
            if (!is_null($lastDate)) {
                $this->assertGreaterThanOrEqual(0, strcmp($post->post_date, $lastDate));
            }
            $lastDate = $post->post_date;
        }

        $posts = Post::orderBy('post_date', 'desc')->get();

        $lastDate = null;
        foreach ($posts as $post) {
            if (!is_null($lastDate)) {
                $this->assertLessThanOrEqual(0, strcmp($post->post_date, $lastDate));
            }
            $lastDate = $post->post_date;
        }
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

        $this->assertEquals(true, $post->hasTerm('category', 'php'));
        $this->assertEquals(false, $post->hasTerm('category', 'not-term'));
        $this->assertEquals(false, $post->hasTerm('no-category', 'php'));
        $this->assertEquals(false, $post->hasTerm('no-category', 'no-term'));

        $this->assertEquals('php', $post->main_category);
        $this->assertEquals(['php'], $post->keywords);
        $this->assertEquals('php', $post->keywords_str);
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
        $post = new Post();
        $post->save();

        $post->meta->username = 'juniorgrossi';
        $post->meta->url = 'http://grossi.io';
        $post->save();

        $post = Post::find($post->ID);
        $this->assertEquals($post->meta->username, 'juniorgrossi');
        $this->assertEquals($post->meta->url, 'http://grossi.io');
    }

    public function testAuthorFields()
    {
        $post = Post::find(1);
        $this->assertEquals($post->author->display_name, 'admin');
        $this->assertEquals($post->author->user_email, 'juniorgro@gmail.com');
    }

    public function testCustomFieldWithAccessors()
    {
        $post = Post::find(1);
        $post->meta->title = 'New title';
        $post->save();

        $this->assertEquals($post->post_title, $post->title);
        $this->assertEquals($post->title, 'Hello world!');
        $this->assertEquals($post->meta->title, 'New title');
    }

    public function testSingleTableInheritance()
    {
        Post::registerPostType('page', "\\Corcel\\Page");

        $page = Post::type('page')->first();

        $this->assertInstanceOf("\\Corcel\\Page", $page);
    }

    public function testClearRegisteredPostTypes()
    {
        Post::registerPostType('page', "\\Corcel\\Page");
        Post::clearRegisteredPostTypes();

        $page = Post::type('page')->first();

        $this->assertInstanceOf("\\Corcel\\Post", $page);
    }

    public function testPostRelationConnections()
    {
        $post = Post::find(1);
        $post->setConnection('no_prefix');

        $this->assertEquals('no_prefix', $post->author->getConnectionName());
    }
}
