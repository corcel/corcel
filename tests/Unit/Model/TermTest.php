<?php

namespace Corcel\Tests\Unit\Model;

use Corcel\Model\Post;
use Corcel\Model\Taxonomy;
use Corcel\Model\Term;

/**
 * Class TermTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class TermTest extends \Corcel\Tests\TestCase
{
    /**
     * @test
     */
    public function it_can_create_term_meta()
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
    public function it_can_create_meta_using_helper_method()
    {
        $term = factory(Term::class)->create();

        $term->saveMeta('foo', 'bar');
        $meta = $term->meta;

        $this->assertNotEmpty($term->meta);
        $this->assertGreaterThan(0, $meta->count());
        $this->assertEquals('bar', $term->meta->foo);
    }

    /**
     * @test
     */
    public function it_has_meta_relation()
    {
        $term = $this->createTermWithTwoMetaFields();

        $count = $term->meta->count();

        $this->assertEquals(2, $count);
    }

    /**
     * @test
     */
    public function its_meta_can_be_queried_by_its_relation()
    {
        $term = $this->createTermWithTwoMetaFields();

        $meta = $term->meta()->where('meta_key', 'foo')->first();

        $this->assertEquals('bar', $meta->meta_value);
    }

    /**
     * @test
     */
    public function it_can_be_added_at_post_level()
    {
        /** @var Post $post */
        $post = factory(Post::class)->create();

        $this->assertEmpty($post->terms);
        $term = $post->addTerm('category', 'foo');
        $post->refresh();

        $this->assertInstanceOf(Term::class, $term);
        $this->assertTrue($post->hasTerm('category', 'foo'));
        $this->assertFalse($post->hasTerm('category', 'bar'));
        $this->assertEquals($term->taxonomy->taxonomy, 'category');
    }

    /**
     * @test
     */
    public function it_is_not_overridden_when_adding_at_post_level()
    {
        /** @var Post $post */
        $post = factory(Post::class)->create();

        $term1 = $post->addTerm('category', 'foo');
        $term2 = $post->addTerm('category', 'foo');
        $post->refresh();

        $this->assertEquals($term1->term_id, $term2->term_id);
        $this->assertEquals($term1->taxonomy, $term2->taxonomy);
        $this->assertCount(1, Taxonomy::all());
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
