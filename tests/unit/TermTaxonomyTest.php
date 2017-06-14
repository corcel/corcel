<?php

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
        $taxonomies = $this->buildTaxonomies();
        $term = $taxonomies->first()->term;

        $taxonomies = Taxonomy::slug($term->slug)->get();

        foreach ($taxonomies as $taxonomy) {
            $this->assertEquals('foo', $taxonomy->taxonomy);
            $this->assertNotNull($taxonomy->term_id);
            $this->assertEquals($taxonomy->term_id, $taxonomy->term->term_id);
        }
    }
    
    public function testGeneralTaxonomy()
    {
        $cat = Taxonomy::category()->slug('php')->posts()->first();
        $this->assertEquals('php', $cat->name);

        $cat = Taxonomy::where('taxonomy', 'category')->slug('php')->with('posts')->get();
        $cat->each(function ($category) {
            $this->assertEquals('php', $category->name);
        });

        $cat = Category::slug('php')->posts()->first();
        $post = $cat->posts()->first();
        $this->assertEquals('hello-world', $post->post_name);
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
    private function buildTaxonomies()
    {
        $terms = factory(Term::class, 2)->create();
        $taxonomies = collect();

        foreach ($terms as $term) {
            $taxonomies->push(factory(TermTaxonomy::class)->create([
                'term_id' => $term->term_id,
                'taxonomy' => 'foo',
            ]));
        }

        return $taxonomies;
    }
}
