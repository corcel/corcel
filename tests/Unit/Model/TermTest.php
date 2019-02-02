<?php

namespace Corcel\Tests\Unit\Model;

use Corcel\Model\Taxonomy;
use Corcel\Model\Term;

/**
 * Class TermTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class TermTest extends \Corcel\Tests\TestCase
{
    public function test_it_can_create_term_meta()
    {
        $term = factory(Term::class)->create();

        $meta = $term->meta()->create([
            'meta_key' => 'foo',
            'meta_value' => 'bar',
        ]);

        $this->assertEquals('foo', $meta->meta_key);
        $this->assertEquals('bar', $meta->meta_value);
    }

    public function test_it_can_create_meta_using_helper_method()
    {
        $term = factory(Term::class)->create();

        $term->saveMeta('foo', 'bar');
        $meta = $term->meta;

        $this->assertNotEmpty($term->meta);
        $this->assertGreaterThan(0, $meta->count());
        $this->assertEquals('bar', $term->meta->foo);
    }

    public function test_it_has_meta_relation()
    {
        $term = $this->createTermWithTwoMetaFields();

        $count = $term->meta->count();

        $this->assertEquals(2, $count);
    }

    public function test_its_meta_can_be_queried_by_its_relation()
    {
        $term = $this->createTermWithTwoMetaFields();

        $meta = $term->meta()->where('meta_key', 'foo')->first();

        $this->assertEquals('bar', $meta->meta_value);
    }

    private function createTermWithTwoMetaFields(): Term
    {
        $term = factory(Term::class)->create();

        $term->saveMeta('foo', 'bar');
        $term->saveMeta('fee', 'baz');

        return $term;
    }

    public function test_it_can_be_queried_with_specified_taxonomy()
    {
        $this->createTermsAssignedToTaxonomy('foo', 5);

        $terms = Term::whereTaxonomy('foo')->get();

        $this->assertCount(5, $terms);
        $this->assertInstanceOf(Term::class, $terms->first());
    }

    public function test_it_can_be_queried_with_specified_taxonomies()
    {
        $this->createTermsAssignedToTaxonomy('foo', 5);
        $this->createTermsAssignedToTaxonomy('bar', 2);

        $terms = Term::whereTaxonomies(['foo', 'bar'])->get();

        $this->assertCount(7, $terms);
        $this->assertInstanceOf(Term::class, $terms->first());
    }

    public function test_it_returns_empty_collection_with_unknown_taxonomy()
    {
        $this->createTermsAssignedToTaxonomy('foo', 1);

        $terms = Term::whereTaxonomy('unknown')->get();

        $this->assertCount(0, $terms);
    }

    private function createTermsAssignedToTaxonomy($taxonomy, $termCount)
    {
        factory(Taxonomy::class, $termCount)->create([
            'taxonomy' => $taxonomy,
            'term_id'  => function () {
                return factory(Term::class)->create()->term_id;
            },
        ]);
    }
}
