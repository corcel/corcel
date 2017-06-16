<?php

use Corcel\Term;
use Corcel\TermMeta;

/**
 * Class TermTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class TermTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function term_can_create_term_meta()
    {
        $term = factory(Term::class)->create();

        $meta = $term->meta()->create([
            'meta_key' => 'foo',
            'meta_value' => 'bar',
        ]);

        $this->assertEquals('foo', $meta->meta_key);
        $this->assertEquals('bar', $meta->meta_value);
    }

    /**
     * @test
     */
    public function term_can_create_meta_using_helper_method()
    {
        $term = factory(Term::class)->create();

        $term->saveMeta('foo', 'bar');
        $meta = $term->meta;

        $this->assertNotEmpty($term->meta);
        $this->assertTrue($meta->count() > 0);
        $this->assertEquals('bar', $term->meta->foo);
    }

    /**
     * @test
     */
    public function term_has_meta_associated()
    {
        $term = $this->createTermWithTwoMetaFields();

        $count = $term->meta->count();

        $this->assertEquals(2, $count);
    }

    /**
     * @test
     */
    public function term_meta_can_be_queried_by_its_relation()
    {
        $term = $this->createTermWithTwoMetaFields();

        $meta = $term->meta()->where('meta_key', 'foo')->first();

        $this->assertEquals('bar', $meta->meta_value);
    }

    /**
     * @return Term
     */
    private function createTermWithTwoMetaFields()
    {
        $term = factory(Term::class)->create();

        $term->saveMeta('foo', 'bar');
        $term->saveMeta('fee', 'baz');

        return $term;
    }
}
