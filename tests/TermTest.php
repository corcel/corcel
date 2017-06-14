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
}