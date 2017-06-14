<?php

use Corcel\Term;

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
        $term = Term::find(2);

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
    public function term_has_meta_associated()
    {
        $term = Term::find(2);

        $count = $term->meta->count();

        $this->assertGreaterThan(0, $count);
    }

    /**
     * @test
     */
    public function term_meta_can_be_queried_by_its_relation()
    {
        $term = Term::find(2);

        $meta = $term->meta()->where('meta_key', 'foo')->first();

        $this->assertEquals('bar', $meta->meta_value);
    }
}
