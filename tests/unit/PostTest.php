<?php

use Corcel\Post;
use Corcel\Page;
use Corcel\PostMetaCollection;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

/**
 * Class PostTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class PostTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function post_has_the_correct_class_name()
    {
        $post = factory(Post::class)->create();

        $this->assertInstanceOf(Post::class, $post);
    }

    /**
     * @test
     */
    public function post_has_the_correct_id()
    {
        $post = factory(Post::class)->create(['ID' => 1000]);

        $this->assertEquals(1000, $post->ID);
    }

    /**
     * @test
     */
    public function post_has_the_correct_type()
    {
        $page = factory(Post::class)->create(['post_type' => 'page']);

        $this->assertEquals($page->post_type, 'page');
    }

    /**
     * Tests the post accessors
     * Accessors should be equal to the original value.
     *
     * @test
     */
    public function post_has_correct_accessors()
    {
        $post = factory(Post::class)->create();

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
        $this->assertEquals($post->post_excerpt, $post->excerpt);
        $this->assertEquals($post->post_status, $post->status);
    }

    /**
     * @test
     */
    public function post_can_accept_unicode_chars()
    {
        $post = factory(Post::class)->create([
            'post_content' => 'test utf8 é à',
            'post_excerpt' => 'test chinese characters お問い合わせ',
        ]);

        $this->assertEquals('test utf8 é à', $post->post_content);
        $this->assertEquals('test chinese characters お問い合わせ', $post->post_excerpt);
    }

    /**
     * @test
     */
    public function post_has_custom_fields()
    {
        $post = factory(Post::class)->create();

        $post->meta()->create([
            'meta_key' => 'foo',
            'meta_value' => 'bar',
        ]);

        $this->assertNotEmpty($post->meta);
        $this->assertNotEmpty($post->fields);
        $this->assertInstanceOf(PostMetaCollection::class, $post->meta);
    }

    /**
     * @test
     */
    public function posts_can_be_ordered_ascending()
    {
        factory(Post::class, 2)->create();

        $posts = Post::orderBy('post_date', 'asc')->get();
        $first = $posts->first();
        $last = $posts->last();

        $this->assertTrue($first->post_date->lessThanOrEqualTo($last->post_date));
        $this->assertTrue($last->post_date->greaterThanOrEqualTo($first->post_date));
    }

    /**
     * @test
     */
    public function posts_can_be_ordered_descending()
    {
        factory(Post::class, 2)->create();

        $posts = Post::orderBy('post_date', 'desc')->get();
        $last = $posts->first();
        $first = $posts->last();

        $this->assertTrue($first->post_date->lessThanOrEqualTo($last->post_date));
        $this->assertTrue($last->post_date->greaterThanOrEqualTo($first->post_date));
    }

    public function testTaxonomies()
    {
        $post = Post::find(1);
        $taxonomy = $post->taxonomies()->first();
        $this->assertEquals($taxonomy->taxonomy, 'category');

        $post = Post::taxonomy('category', ['php'])->first();
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
        Post::registerPostType('page', '\\Corcel\\Page');

        $page = Post::type('page')->first();

        $this->assertInstanceOf('\\Corcel\\Page', $page);
    }

    public function testClearRegisteredPostTypes()
    {
        Post::registerPostType('page', '\\Corcel\\Page');
        Post::clearRegisteredPostTypes();

        $page = Post::type('page')->first();

        $this->assertInstanceOf('\\Corcel\\Post', $page);
    }

    public function testPostRelationConnections()
    {
        $post = Post::find(1);
        $post->setConnection('no_prefix');

        $this->assertEquals('no_prefix', $post->author->getConnectionName());
    }

    public function testPostTypeIsFillable()
    {
        $postType = 'video';
        $post = new Post(['post_type' => $postType]);
        $this->assertEquals($postType, $post->post_type);
    }

    /**
     * This tests to ensure that when the post_parent is 0, it returns 0 and not null
     * Ocde in the Post::_get() method only checked if the value was false, and so
     * wouldn't return values from the model that were false (like 0).
     */
    public function testPostParentDoesNotReturnNullWhenItIsZero()
    {
        $post = Post::find(1);

        $this->assertNotNull($post->post_parent);
    }

    public function testAddShortcode()
    {
        Post::addShortcode('gallery', function (ShortcodeInterface $s) {
            return $s->getName().'.'.$s->getParameter('id').'.'.$s->getParameter('size');
        });

        $post = Post::find(123);

        $this->assertEquals($post->content, 'test gallery.123.medium shortcodes');
    }

    public function testMultipleShortcodes()
    {
        $post = Post::find(125);

        $this->assertEquals($post->content, '1~gallery.1.small2~gallery.2.medium');
    }

    public function testRemoveShortcode()
    {
        Post::removeShortcode('gallery');

        $post = Post::find(123);

        $this->assertEquals($post->content, 'test [gallery id="123" size="medium"] shortcodes');
    }

    public function testPostFormat()
    {
        $post = Post::find(3);
        $this->assertEquals('video', $post->getFormat());
        $post = Post::find(1);
        $this->assertFalse($post->getFormat());
    }
}
