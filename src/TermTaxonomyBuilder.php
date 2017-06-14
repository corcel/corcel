<?php

namespace Corcel;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class TermTaxonomyBuilder
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 * @author Yoram de Langen <yoramdelangen@gmail.com>
 */
class TermTaxonomyBuilder extends Builder
{
    /**
     * Add posts to the relationship builder.
     *
     * @return TermTaxonomyBuilder
     */
    public function posts()
    {
        return $this->with('posts');
    }

    /**
     * Set taxonomy type to category.
     *
     * @return TermTaxonomyBuilder
     */
    public function category()
    {
        return $this->where('taxonomy', 'category');
    }

    /**
     * Set taxonomy type to nav_menu.
     *
     * @return TermTaxonomyBuilder
     */
    public function menu()
    {
        return $this->where('taxonomy', 'nav_menu');
    }

    /**
     * Get a term taxonomy by specific slug.
     *
     * @param string $slug
     * @return TermTaxonomyBuilder
     */
    public function slug($slug = null)
    {
        if (!is_null($slug) and !empty($slug)) {
            return $this->whereHas('term', function ($query) use ($slug) {
                $query->where('slug', $slug);
            });
        }

        return $this;
    }
}
