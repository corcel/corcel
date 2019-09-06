<?php

namespace Corcel\Tests\Unit\Model;

use Carbon\Carbon;
use Corcel\Model\Collection\MetaCollection;
use Corcel\Model\Page;
use Corcel\Model\Post;
use Corcel\Model\Taxonomy;
use Corcel\Model\Term;
use Corcel\Model\User;
use Corcel\Shortcode;
use Illuminate\Support\Arr;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

/**
 * Class PostTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class PostTest extends \Corcel\Tests\TestCase
{
    public function test_it_has_the_correct_class_name()
    {
        $post = factory(Post::class)->create();

        $this->assertInstanceOf(Post::class, $post);
    }

    public function test_it_has_an_integer_id()
    {
        $post = factory(Post::class)->create();

        $this->assertIsInt($post->ID);
        $this->assertGreaterThan(0, $post->ID);
    }

    public function test_it_has_status_scope()
    {
        factory(Post::class)->create(['post_status' => 'foo']);

        $posts = Post::status('foo')->get();

        $this->assertNotNull($posts);
        $this->assertCount(1, $posts);
    }

    public function test_it_has_has_meta_scope()
    {
        $post = factory(Post::class)->create();
        $post->saveMeta('foo', 'bar');

        $posts = Post::hasMeta('foo')->get();

        $this->assertCount(1, $posts);
        $this->assertInstanceOf(Post::class, $posts->first());

        $newPost = Post::hasMeta('foo', 'bar')->first();
        $this->assertEquals($post->title, $newPost->title);
        $this->assertEquals($post->ID, $newPost->ID);
    }

    public function test_it_has_published_scope()
    {
        factory(Post::class)->create(['post_status' => 'publish']);

        $posts = Post::published()->get();

        $this->assertNotNull($posts);
        $this->assertGreaterThan(0, $posts->count());
    }

    public function test_it_has_type_scope()
    {
        factory(Post::class)->create(['post_type' => 'foo']);

        $posts = Post::type('foo')->get();

        $this->assertNotNull($posts);
        $this->assertCount(1, $posts);
    }

    public function test_it_has_type_in_scope()
    {
        factory(Post::class)->create(['post_type' => 'blue']);
        factory(Post::class)->create(['post_type' => 'red']);
        factory(Post::class)->create(['post_type' => 'yellow']);

        $posts = Post::typeIn(['blue', 'yellow'])->get();

        $this->assertNotNull($posts);
        $this->assertCount(2, $posts);
    }

    public function test_it_has_slug_scope()
    {
        factory(Post::class)->create(['post_name' => 'my-fake-post-slug']);

        $posts = Post::slug('my-fake-post-slug')->get();

        $this->assertNotNull($posts);
        $this->assertCount(1, $posts);
    }

    public function test_it_has_taxonomy_scope()
    {
        $this->createPostWithTaxonomiesAndTerms();

        $posts = Post::taxonomy('foo', 'bar')->get();
        $this->assertNotNull($posts);
        $this->assertGreaterThan(0, $posts->count());

        $posts = Post::taxonomy('foo', ['bar'])->get();
        $this->assertNotNull($posts);
        $this->assertGreaterThan(0, $posts->count());
    }

    public function test_it_has_children_relation()
    {
        $post = factory(Post::class)->create();
        factory(Post::class)->create(['post_parent' => $post->ID]);
        factory(Post::class)->create(['post_parent' => $post->ID]);
        factory(Post::class)->create(['post_parent' => $post->ID]);

        $children = $post->children;

        $this->assertCount(3, $children);
        $this->assertInstanceOf(Post::class, $children->first());
        $this->assertEquals($post->ID, $children->first()->post_parent);
    }

    public function test_it_can_be_ordered()
    {
        $older = Carbon::now()->subYears(10);

        $firstPost = factory(Post::class)->create(['post_date' => $older]);
        factory(Post::class)->create(['post_date' => $older->addMonths(1)]);
        $lastPost = factory(Post::class)->create(['post_date' => $older->addMonths(2)]);

        $newest = Post::newest()->first();
        $oldest = Post::oldest()->first();

        $this->assertEquals($firstPost->post_name, $oldest->post_name);
        $this->assertEquals($firstPost->post_title, $oldest->post_title);
        $this->assertEquals($lastPost->post_name, $newest->post_name);
        $this->assertEquals($lastPost->post_title, $newest->post_title);
    }

    public function test_it_can_have_different_post_type()
    {
        $page = factory(Post::class)->create(['post_type' => 'page']);

        $this->assertEquals($page->post_type, 'page');
    }

    public function test_it_has_aliases()
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

    public function test_it_has_isset_method_working()
    {
        $post = factory(Post::class)->create();
        $post->createMeta('foo', 'bar');

        $this->assertTrue(isset($post->meta->foo));
    }

    public function test_it_can_add_alias_in_runtime()
    {
        $post = factory(Post::class)->create();
        $post->saveMeta('foo', 'bar');

        $post->addAlias('baz', ['meta' => 'foo']);
        $this->assertEquals('bar', $post->baz);
        $this->assertEquals($post->baz, $post->meta->foo);

        Post::addAlias('fee', ['meta' => 'foo']);
        $this->assertEquals('bar', $post->fee);
        $this->assertEquals($post->fee, $post->meta->foo);
    }

    public function test_it_can_accept_unicode_chars()
    {
        $post = factory(Post::class)->create([
            'post_content' => 'test utf8 é à',
            'post_excerpt' => 'test chinese characters お問い合わせ',
        ]);

        $this->assertEquals('test utf8 é à', $post->post_content);
        $this->assertEquals('test chinese characters お問い合わせ', $post->post_excerpt);
    }

    public function test_it_can_have_custom_fields()
    {
        $post = factory(Post::class)->create();

        $post->meta()->create([
            'meta_key' => 'foo',
            'meta_value' => 'bar',
        ]);

        $this->assertNotEmpty($post->meta);
        $this->assertNotEmpty($post->fields);
        $this->assertInstanceOf(MetaCollection::class, $post->meta);
    }

    public function test_it_can_add_custom_fields()
    {
        $post = factory(Post::class)->create();

        $post->saveMeta('foo', 'bar');
        $meta = $post->meta()->orderBy('meta_id', 'desc')->first();

        $this->assertEquals('foo', $meta->meta_key);
        $this->assertEquals('bar', $meta->meta_value);
    }

    public function test_it_can_add_custom_fields_using_save_field_method()
    {
        $post = factory(Post::class)->create();

        $post->saveField('foo', 'bar');
        $meta = $post->meta->first();

        $this->assertEquals('foo', $meta->meta_key);
        $this->assertEquals('bar', $meta->meta_value);
    }

    public function test_it_can_save_multiple_meta_at_the_same_time()
    {
        $post = factory(Post::class)->create();

        $post->saveMeta([
            'foo' => 'bar',
            'fee' => 'baz',
        ]);

        $this->assertCount(2, $post->meta);
        $this->assertEquals('bar', $post->meta->foo);
        $this->assertEquals('baz', $post->meta->fee);
    }

    public function test_they_can_be_ordered_ascending()
    {
        factory(Post::class, 2)->create();

        $posts = Post::query()->orderBy('post_date', 'asc')->get();
        $first = $posts->first();
        $last = $posts->last();

        $this->assertTrue($first->post_date->lessThanOrEqualTo($last->post_date));
        $this->assertTrue($last->post_date->greaterThanOrEqualTo($first->post_date));
    }

    public function test_they_can_be_ordered_descending()
    {
        factory(Post::class, 2)->create();

        $posts = Post::orderBy('post_date', 'desc')->get();
        $last = $posts->first();
        $first = $posts->last();

        $this->assertTrue($first->post_date->lessThanOrEqualTo($last->post_date));
        $this->assertTrue($last->post_date->greaterThanOrEqualTo($first->post_date));
    }

    public function test_it_can_be_paginated()
    {
        $post = factory(Post::class)->create();
        factory(Post::class)->create();
        factory(Post::class)->create();

        /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
        $paginator = Post::paginate(2);
        $firstPost = Arr::first($paginator->items());

        $this->assertEquals(2, $paginator->perPage());
        $this->assertEquals(2, $paginator->count());
        $this->assertEquals(3, $paginator->total());
        $this->assertInstanceOf(Post::class, $firstPost);
        $this->assertEquals($post->post_title, $firstPost->post_title);
        $this->assertRegExp('/\<nav\>\s*\<ul class="pagination/', $paginator->toHtml());
    }
    
    public function test_it_can_have_taxonomy()
    {
        $post = $this->createPostWithTaxonomiesAndTerms();

        $this->assertEquals(1, $post->taxonomies->count());
        $this->assertEquals('foo', $post->taxonomies->first()->taxonomy);
    }

    public function test_it_can_have_taxonomy_and_terms()
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

    public function test_it_can_have_term()
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

    public function test_it_can_have_author_relation()
    {
        $post = $this->createPostWithAuthor();

        $this->assertEquals('Administrator', $post->author->display_name);
        $this->assertEquals('admin@example.com', $post->author->user_email);
    }

    public function test_it_has_the_correct_instance_name_if_it_is_a_custom_post_type()
    {
        factory(Post::class)->create(['post_type' => 'page']);
        Post::registerPostType('page', Page::class);

        $page = Post::orderBy('ID', 'desc')->first();

        $this->assertInstanceOf(Page::class, $page);
    }

    public function test_it_has_its_instance_name_back_to_post_after_clearing_post_types()
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

    public function test_its_relation_can_have_different_database_connection()
    {
        $post = factory(Post::class)->make();
        $post->setConnection('foo');
        $post->author()->associate(factory(User::class)->create());
        $post->save();

        $this->assertEquals('foo', $post->author->getConnectionName());
    }

    public function test_its_type_is_fillable()
    {
        $post = factory(Post::class)->create(['post_type' => 'video']);

        $this->assertEquals('video', $post->post_type);
    }

    public function test_its_parent_does_not_return_null_when_it_is_zero()
    {
        $post = factory(Post::class)->create(['post_parent' => 0]);

        $this->assertNotNull($post->post_parent);
        $this->assertEquals(0, $post->post_parent);
    }

    public function test_parent_relation()
    {
        $parent = factory(Post::class)->create();
        $post = factory(Post::class)->create(['post_parent' => $parent->ID]);

        $this->assertEquals($parent->fresh(), $post->parent);
    }

    public function test_attachment_relation()
    {
        $parent = factory(Post::class)->create();
        $attachment = factory(Post::class)->create([
            'post_parent' => $parent->ID,
            'post_type' => 'attachment',
        ]);

        $this->assertEquals($attachment->fresh(), $parent->attachment->first());
    }

    public function test_revision_relation()
    {
        $parent = factory(Post::class)->create();
        $revision = factory(Post::class)->create([
            'post_parent' => $parent->ID,
            'post_type' => 'revision',
        ]);

        $revisions = $parent->revision;
        $this->assertEquals($revision->fresh(), $revisions->first());
    }

    public function test_it_can_have_shortcode()
    {
        $this->registerFooShortcode();

        $post = factory(Post::class)->create([
            'post_content' => 'test [foo a="bar" b="baz"]',
        ]);

        $this->assertEquals($post->content, 'test foo.bar.baz');
    }

    public function test_it_can_have_shortcode_from_config_file()
    {
        $post = factory(Post::class)->create([
            'post_content' => 'foo [fake one="two"]',
        ]);

        $this->assertEquals('foo html-for-shortcode-fake-two', $post->content);
    }

    public function test_its_content_can_have_multiple_shortcodes()
    {
        $this->registerFooShortcode();

        $post = factory(Post::class)->create([
            'post_content' => '1~[foo a="bar" b="baz"] 2~[foo a="baz" b="bar"]',
        ]);

        $this->assertEquals($post->content, '1~foo.bar.baz 2~foo.baz.bar');
    }

    public function test_its_shortcode_can_be_removed()
    {
        $this->registerFooShortcode();
        Post::removeShortcode('foo');

        $post = factory(Post::class)->create([
            'post_content' => 'test [foo a="bar" b="baz"]',
        ]);

        $this->assertEquals($post->content, 'test [foo a="bar" b="baz"]');
    }

    public function test_it_can_have_post_format()
    {
        $post = $this->createPostWithPostFormatTaxonomy();

        $this->assertEquals('foo', $post->getFormat());
    }

    public function test_it_can_have_false_post_format()
    {
        $post = factory(Post::class)->create();

        $this->assertFalse($post->getFormat());
    }

    public function test_it_has_correct_post_type_with_callback_in_where()
    {
        $query = Page::where(function ($q) {
            $q->where('foo', 'bar');
        });

        $expectedQuery = 'select * from "wp_posts" where "post_type" = ? and ("foo" = ?)';
        $expectedBindings = ['page', 'bar'];

        $this->assertEquals($expectedQuery, $query->toSql());
        $this->assertSame($expectedBindings, $query->getBindings());
    }

    public function test_its_search_has_correct_empty_word_query()
    {
        $emptyWord = Post::search();

        $expectedEmptyWordQuery = 'select * from "wp_posts"';
        $expectedEmptyWordBindings = [];

        $this->assertEquals($expectedEmptyWordQuery, $emptyWord->toSql());
        $this->assertSame($expectedEmptyWordBindings, $emptyWord->getBindings());
    }

    public function test_its_search_has_correct_single_word_query()
    {
        $singleWord = Post::search('foo');

        $expectedSingleWordQuery = 'select * from "wp_posts" where ("post_title" like ? or "post_excerpt" like ? or "post_content" like ?)';
        $expectedSingleWordBindings = ['%foo%', '%foo%', '%foo%'];

        $this->assertEquals($expectedSingleWordQuery, $singleWord->toSql());
        $this->assertSame($expectedSingleWordBindings, $singleWord->getBindings());
    }

    public function test_its_search_has_correct_multiple_word_query()
    {
        $multipleWord = Post::search('foo bar');

        $expectedMultipleWordQuery = 'select * from "wp_posts" where ("post_title" like ? or "post_excerpt" like ? or "post_content" like ? or "post_title" like ? or "post_excerpt" like ? or "post_content" like ?)';
        $expectedMultipleWordBindings = ['%foo%', '%foo%', '%foo%', '%bar%', '%bar%', '%bar%'];

        $this->assertEquals($expectedMultipleWordQuery, $multipleWord->toSql());
        $this->assertSame($expectedMultipleWordBindings, $multipleWord->getBindings());
    }

    public function test_its_search_has_correct_multiple_word_in_array_query()
    {
        $multipleWordArray = Post::search(['foo', 'bar']);

        $expectedMultipleWordQuery = 'select * from "wp_posts" where ("post_title" like ? or "post_excerpt" like ? or "post_content" like ? or "post_title" like ? or "post_excerpt" like ? or "post_content" like ?)';
        $expectedMultipleWordBindings = ['%foo%', '%foo%', '%foo%', '%bar%', '%bar%', '%bar%'];

        $this->assertEquals($expectedMultipleWordQuery, $multipleWordArray->toSql());
        $this->assertSame($expectedMultipleWordBindings, $multipleWordArray->getBindings());
    }

    public function test_its_search_for_different_post_types_is_correct()
    {
        $singleWord = Page::search('foo');
        $multipleWord = Page::search('foo bar');

        $expectedSingleWordQuery = 'select * from "wp_posts" where "post_type" = ? and ("post_title" like ? or "post_excerpt" like ? or "post_content" like ?)';
        $expectedMultipleWordQuery = 'select * from "wp_posts" where "post_type" = ? and ("post_title" like ? or "post_excerpt" like ? or "post_content" like ? or "post_title" like ? or "post_excerpt" like ? or "post_content" like ?)';

        $this->assertEquals($expectedSingleWordQuery, $singleWord->toSql());
        $this->assertEquals($expectedMultipleWordQuery, $multipleWord->toSql());
    }

    private function createPostWithTaxonomiesAndTerms(): Post
    {
        $post = factory(Post::class)->create();

        $post->taxonomies()->attach(
            factory(Taxonomy::class)->create([
                'taxonomy' => 'foo',
            ])->term_taxonomy_id, [
                'term_order' => 0,
            ]
        );

        return $post;
    }

    private function createPostWithAuthor(): Post
    {
        $post = factory(Post::class)->create();

        $post->author()->associate(
            factory(User::class)->create()
        );

        return $post;
    }

    private function registerFooShortcode(): void
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

    private function createPostWithPostFormatTaxonomy(): Post
    {
        $post = factory(Post::class)->create();

        $post->taxonomies()->attach(
            factory(Taxonomy::class)->create([
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

class FakeShortcode implements Shortcode
{
    public function render(ShortcodeInterface $shortcode): string
    {
        return sprintf(
            'html-for-shortcode-%s-%s',
            $shortcode->getName(),
            $shortcode->getParameter('one')
        );
    }
}
