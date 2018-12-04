<?php

namespace Corcel\Tests\Unit\Model;

use Corcel\Model\Post;
use Corcel\Model\Taxonomy;
use Corcel\Model\Term;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class TermTaxonomyTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class TaxonomyTest extends \Corcel\Tests\TestCase
{
    public function test_it_belongs_to_a_term()
    {
        $taxonomy = factory(Taxonomy::class)->create([
            'term_id' => 0,
            'count' => 1,
        ]);

        $term = factory(Term::class)->create();
        $taxonomy->term()->associate($term);

        $this->assertEquals($term->term_id, $taxonomy->term_id);
    }

    public function test_it_can_filter_taxonomy_by_term()
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

    public function test_it_can_be_queried_by_name_and_term_slug()
    {
        $this->createTaxonomyWithTermsAndPosts();

        $foo = Taxonomy::name('foo')->slug('bar')->first();
        $this->assertEquals('Bar', $foo->name);

        $foo = Taxonomy::name('foo')->slug('bar')->get();
        $foo->each(function ($foo) {
            $this->assertEquals('Bar', $foo->name);
            $this->assertEquals('bar', $foo->slug);
        });
    }

    public function test_it_can_be_queries_by_term_as_an_aliases_to_slug()
    {
        $this->createTaxonomyWithTermsAndPosts();

        $foo = Taxonomy::name('foo')->term('bar')->first();

        $this->assertEquals('Bar', $foo->name);
    }

    public function test_it_can_query_taxonomy_by_term_and_get_all_posts_related()
    {
        $this->createTaxonomyWithTermsAndPosts();

        $post = Taxonomy::name('foo')->slug('bar')
            ->orderBy('term_taxonomy_id', 'desc')->first()
            ->posts->first();

        $this->assertEquals('Foo bar', $post->title);
    }

    public function test_its_first_post_should_have_keywords_if_it_has_taxonomy_and_term()
    {
        $taxonomy = $this->createTaxonomyWithTermsAndPosts();

        $post = $taxonomy->posts->first();

        $this->assertGreaterThan(0, count($post->keywords));
    }

    public function test_it_has_correct_query_with_callback_in_where()
    {
        /** @var Builder $query */
        $query = Category::query()->where(function (Builder $q) {
            $q->where('foo', 'bar');
        });

        $expectedQuery = 'select * from "wp_term_taxonomy" where "taxonomy" = ? and ("foo" = ?)';
        $expectedBindings = ['category', 'bar'];

        $this->assertEquals($expectedQuery, $query->toSql());
        $this->assertSame($expectedBindings, $query->getBindings());
    }

    public function test_it_has_meta_relation()
    {
        /** @var Taxonomy $taxonomy */
        $taxonomy = factory(Taxonomy::class)->create();
        /** @var Term $term */
        $term = $taxonomy->term;
        $term->saveMeta('foo', 'bar');

        $this->assertNotEmpty($taxonomy->meta);
        $this->assertEquals('bar', $taxonomy->meta->foo);
    }

    public function test_it_has_parent()
    {
        /** @var Taxonomy $parent */
        $parent = factory(Taxonomy::class)->create();
        /** @var Taxonomy $taxonomy */
        $taxonomy = factory(Taxonomy::class)->create(['parent' => $parent->term_taxonomy_id]);

        $this->assertEquals($parent->fresh(), $taxonomy->parent()->first());
    }

    private function createTaxonomyWithTermsAndPosts(): Taxonomy
    {
        $taxonomy = factory(Taxonomy::class)->create([
            'taxonomy' => 'foo',
            'term_id' => function () {
                return factory(Term::class)->create([
                    'name' => 'Bar',
                    'slug' => 'bar',
                ])->term_id;
            }
        ]);

        $post = factory(Post::class)->create([
            'post_title' => 'Foo bar',
        ]);

        $post->taxonomies()->attach($taxonomy->term_taxonomy_id);

        return $taxonomy;
    }
}

class Category extends Taxonomy
{
    protected $taxonomy = 'category';
}
