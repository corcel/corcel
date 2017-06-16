<?php

use Corcel\Post;
use Corcel\Term;
use Corcel\TermTaxonomy;
use Illuminate\Support\Str;

/**
 * Class TermTaxonomyTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class TermTaxonomyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function taxonomy_belongs_to_term()
    {
        $taxonomy = factory(TermTaxonomy::class)->create([
            'term_id' => 0,
            'count' => 1,
        ]);

        $term = factory(Term::class)->create();
        $taxonomy->term()->associate($term);

        $this->assertEquals($term->term_id, $taxonomy->term_id);
    }

    /**
     * @test
     */
    public function can_filter_taxonomy_by_term()
    {
        $taxonomy = $this->createTaxonomyWithTermsAndPosts();
        $term = $taxonomy->term;

        $taxonomies = Taxonomy::slug($term->slug)->get();

        foreach ($taxonomies as $taxonomy) {
            $this->assertEquals('foo', $taxonomy->taxonomy);
            $this->assertNotNull($taxonomy->term_id);
            $this->assertEquals($taxonomy->term_id, $taxonomy->term->term_id);
        }
    }

    /**
     * @test
     */
    public function taxonomy_can_be_queried_by_name_and_term_slug()
    {
        $taxonomy = $this->createTaxonomyWithTermsAndPosts();

        // TODO This TermTaxonomyBuilder::posts() method is wrong. posts() should be a relation not a with()
        // TODO Maybe change slug() to term()
        $foo = Taxonomy::name('foo')->slug('bar')->first();
        $this->assertEquals('Bar', $foo->name);

        $foo = Taxonomy::name('foo')->slug('bar')->get();
        $foo->each(function ($foo) {
            $this->assertEquals('Bar', $foo->name);
            $this->assertEquals('bar', $foo->slug);
        });
    }

    /**
     * @test
     */
    public function can_query_taxonomy_by_term_and_get_all_posts_related()
    {
        // missing
    }

    public function testPostKeywords()
    {
        $post = Post::find(16);
        $this->assertTrue(count($post->keywords) > 0);

        $post = Post::find(2);
        $this->assertTrue(count($post->keywords) == 0);
    }

    public function testPageToArray()
    {
        $page = Page::find(2)->toArray();
        $this->assertTrue(is_array($page));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function createTaxonomyWithTermsAndPosts()
    {
        $taxonomy = factory(TermTaxonomy::class)->create([
            'taxonomy' => 'foo',
            'term_id' => function () {
                return factory(Term::class)->create([
                    'name' => 'Bar',
                    'slug' => 'bar',
                ])->term_id;
            }
        ]);

        $post = factory(Post::class)->create();

        $post->taxonomies()->attach($taxonomy->term_taxonomy_id, [
            'term_order' => 0,
        ]);
    }
}
