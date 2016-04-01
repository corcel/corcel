<?php

class TaxonomyTest extends PHPUnit_Framework_TestCase
{
    public function testGeneralTaxonomy()
    {
        $cat = Taxonomy::category()->slug('php')->posts()->first();
        $this->assertEquals('php', $cat->name);

        $cat = Taxonomy::where('taxonomy', 'category')->slug('php')->with('posts')->get();
        $cat->each(function($category) {
            $this->assertEquals('php', $category->name);
        });

        $cat = Category::slug('php')->posts()->first();
        $post = $cat->posts()->first();
        $this->assertEquals('hello-world', $post->post_name);
    }
}