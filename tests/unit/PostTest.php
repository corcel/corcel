<?php

use Corcel\Post;
use Corcel\Page;
use Corcel\PostMetaCollection;
use Corcel\Term;
use Corcel\TermTaxonomy;
use Corcel\User;
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
    public function post_has_the_is_as_an_integer()
    {
        $post = factory(Post::class)->create();

        $this->assertTrue(is_int($post->ID));
        $this->assertGreaterThan(0, $post->ID);
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
    public function post_can_add_custom_fields()
    {
        $post = factory(Post::class)->create();

        $post->saveMeta('foo', 'bar');
        $meta = $post->meta->first();

        $this->assertEquals('foo', $meta->meta_key);
        $this->assertEquals('bar', $meta->meta_value);
    }

    /**
     * @test
     */
    public function post_can_add_custom_fields_using_an_alias()
    {
        $post = factory(Post::class)->create();

        $post->saveField('foo', 'bar');
        $meta = $post->meta->first();

        $this->assertEquals('foo', $meta->meta_key);
        $this->assertEquals('bar', $meta->meta_value);
    }

    /**
     * @test
     */
    public function post_can_update_meta()
    {
        $post = factory(Post::class)->create();
        $post->saveMeta('foo', 'bar');
        $post->saveMeta('foo', 'baz');

        $meta = $post->meta()->where('meta_key', 'foo')->first();

        $this->assertEquals('baz', $meta->meta_value);
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

    /**
     * @test
     */
    public function post_can_have_taxonomy()
    {
        $post = $this->createPostWithTaxonomiesAndTerms();

        $this->assertEquals(1, $post->taxonomies->count());
        $this->assertEquals('foo', $post->taxonomies->first()->taxonomy);
    }

    /**
     * @test
     */
    public function post_with_taxonomy_and_terms()
    {
        $createdPost = $this->createPostWithTaxonomiesAndTerms();

        $post = Post::orderBy('ID', 'desc')
            ->taxonomy('foo', ['bar'])->first();

        $this->assertNotNull($post);
        $this->assertEquals($createdPost->ID, $post->ID);

        $post = Post::orderBy('ID', 'desc')
            ->taxonomy('foo', 'bar')->first();

        $this->assertNotNull($post);
        $this->assertEquals($createdPost->ID, $post->ID);
    }

    /**
     * @test
     */
    public function post_has_term()
    {
        $post = $this->createPostWithTaxonomiesAndTerms();

        $this->assertEquals(true, $post->hasTerm('foo', 'bar'));
        $this->assertEquals(false, $post->hasTerm('foo', 'baz'));
        $this->assertEquals(false, $post->hasTerm('fee', 'bar'));
        $this->assertEquals(false, $post->hasTerm('fee', 'baz'));
        $this->assertEquals('Bar', $post->main_category);
        $this->assertEquals(['Bar'], $post->keywords);
        $this->assertEquals('Bar', $post->keywords_str);
    }

    /**
     * @test
     */
    public function post_can_update_custom_fields_using_meta_attribute()
    {
        $post = factory(Post::class)->create(['post_name' => 'foo']);
        $post->meta->username = 'jgrossi';
        $post->meta->url = 'http://jgrossi.com';
        $post->save();

        $post = Post::query()->where('post_name', 'foo')->first();

        $this->assertEquals($post->meta->username, 'jgrossi');
        $this->assertEquals($post->meta->url, 'http://jgrossi.com');
    }

    /**
     * @test
     */
    public function post_can_update_custom_fields_using_meta_attribute_and_accessors()
    {
        $post = factory(Post::class)->create(['post_title' => 'Post title']);
        $post->meta->title = 'Meta title';
        $post->save();

        $this->assertEquals($post->post_title, $post->title);
        $this->assertEquals($post->title, 'Post title');
        $this->assertEquals($post->meta->title, 'Meta title');
    }

    /**
     * @test
     */
    public function post_has_author_relation()
    {
        $post = $this->createPostWithAuthor();

        $this->assertEquals('Administrator', $post->author->display_name);
        $this->assertEquals('admin@example.com', $post->author->user_email);
    }

    /**
     * @test
     */
    public function single_table_inheritance()
    {
        factory(Post::class)->create(['post_type' => 'page']);
        Post::registerPostType('page', Page::class);

        $page = Post::orderBy('ID', 'desc')->first();

        $this->assertInstanceOf(Page::class, $page);
    }

    /**
     * @test
     */
    public function clear_registered_post_types()
    {
        factory(Post::class)->create([
            'post_type' => 'page',
            'post_name' => 'foo2',
        ]);
        Post::registerPostType('page', Page::class);
        Post::clearRegisteredPostTypes();

        $page = Post::where('post_name', 'foo2')->first();

        $this->assertInstanceOf(Post::class, $page);
    }

    /**
     * @test
     */
    public function post_relation_can_have_different_database_connection()
    {
        $post = factory(Post::class)->make();
        $post->setConnection('foo');
        $post->author()->associate(factory(User::class)->create());
        $post->save();

        $this->assertEquals('foo', $post->author->getConnectionName());
    }

    /**
     * @test
     */
    public function post_type_is_fillable()
    {
        $post = factory(Post::class)->create(['post_type' => 'video']);

        $this->assertEquals('video', $post->post_type);
    }

    /**
     * @test
     */
    public function post_parent_does_not_return_null_when_it_is_zero()
    {
        $post = factory(Post::class)->create(['post_parent' => 0]);

        $this->assertNotNull($post->post_parent);
        $this->assertEquals(0, $post->post_parent);
    }

    /**
     * @test
     */
    public function post_can_have_shortcode()
    {
        $this->registerFooShortcode();

        $post = factory(Post::class)->create([
            'post_content' => 'test [foo a="bar" b="baz"]',
        ]);

        $this->assertEquals($post->content, 'test foo.bar.baz');
    }

    /**
     * @test
     */
    public function post_content_can_have_multiple_shortcodes()
    {
        $this->registerFooShortcode();

        $post = factory(Post::class)->create([
            'post_content' => '1~[foo a="bar" b="baz"] 2~[foo a="baz" b="bar"]',
        ]);

        $this->assertEquals($post->content, '1~foo.bar.baz 2~foo.baz.bar');
    }

    /**
     * @test
     */
    public function post_shortcode_can_be_removed()
    {
        $this->registerFooShortcode();
        Post::removeShortcode('foo');

        $post = factory(Post::class)->create([
            'post_content' => 'test [foo a="bar" b="baz"]',
        ]);

        $this->assertEquals($post->content, 'test [foo a="bar" b="baz"]');
    }

    /**
     * @test
     */
    public function post_can_have_post_format()
    {
        $post = $this->createPostWithPostFormatTaxonomy();

        $this->assertEquals('foo', $post->getFormat());
    }

    /**
     * @test
     */
    public function post_can_have_false_post_format()
    {
        $post = factory(Post::class)->create();

        $this->assertFalse($post->getFormat());
    }

    /**
     *
     * @return Post
     */
    private function createPostWithTaxonomiesAndTerms()
    {
        $post = factory(Post::class)->create();

        $post->taxonomies()->attach(
            factory(TermTaxonomy::class)->create([
                'taxonomy' => 'foo',
            ])->term_taxonomy_id, [
                'term_order' => 0,
            ]
        );

        return $post;
    }

    /**
     * @return Post
     */
    private function createPostWithAuthor()
    {
        $post = factory(Post::class)->create();

        $post->author()->associate(
            factory(User::class)->create()
        );

        return $post;
    }

    /**
     * @return void
     */
    private function registerFooShortcode()
    {
        Post::addShortcode('foo', function (ShortcodeInterface $shortcode) {
            return sprintf(
                '%s.%s.%s',
                $shortcode->getName(),
                $shortcode->getParameter('a'),
                $shortcode->getParameter('b')
            );
        });
    }

    /**
     * @return Post
     */
    private function createPostWithPostFormatTaxonomy()
    {
        $post = factory(Post::class)->create();

        $post->taxonomies()->attach(
            factory(TermTaxonomy::class)->create([
                'taxonomy' => 'post_format',
                'term_id' => function () {
                    return factory(Term::class)->create([
                        'name' => $name = 'post-format-foo',
                        'slug' => $name,
                    ])->term_id;
                },
            ])->term_taxonomy_id, [
                'term_order' => 0,
            ]
        );

        return $post;
    }
}
