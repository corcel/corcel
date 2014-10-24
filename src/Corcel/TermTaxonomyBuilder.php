<?php

/**
 * Corcel\TermTaxonomyBuilder
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 * @author  Yoram de Langen <yoramdelangen@gmail.com>
 */

namespace Corcel;

use Illuminate\Database\Eloquent\Builder;

class TermTaxonomyBuilder extends Builder
{
    private $category_slug;

    /**
     * Add posts to the relationship builder
     * @return Corcel\TermTaxonomyBuilder
     */
    public function posts()
    {
        return $this->with('posts');
    }

    /**
     * Set taxonomy type to category
     * @return Corcel\TermTaxonomyBuilder
     */
    public function category()
    {
        return $this->where('taxonomy', 'category');
    }

    /**
     * Get a term taxonomy by name/slug
     * @param  string $name
     * @return \Corcel\PostBuilder
     */
    public function name($name)
    {
        // make sure this can only be called when taxonomy is a NAV_MENU
        if ($this->model['taxonomy'] == 'nav_menu') {
            return $this->whereHas('term', function($query) use ($name) {
                $query->where('slug', '=', $name);
            });
        }
        return $this;
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
