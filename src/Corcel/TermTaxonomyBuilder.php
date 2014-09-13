<?php

/**
 * Corcel\TermTaxonomyBuilder
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */

namespace Corcel;

use Illuminate\Database\Eloquent\Builder;

class TermTaxonomyBuilder extends Builder
{
    private $category_slug;

    public function posts()
    {
        return $this->with('posts');
    }

    public function category()
    {
        return $this->where('taxonomy', 'category');
    }

    /**
     * Get only posts with a specific slug
     *
     * @param string slug
     * @return \Corcel\PostBuilder
     */
    public function slug( $category_slug=null )
    {
        if( !is_null($category_slug) and !empty($category_slug) ) {
            // set this category_slug to be used in with callback
            $this->category_slug = $category_slug;

            // exception to filter on slug from category
            $exception = function($query) {
                $query->where('slug', '=', $this->category_slug);
            };

            // load term to filter
            return $this->whereHas('term', $exception);
        }

        return $this;
    }
}
