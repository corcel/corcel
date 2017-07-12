<?php

namespace Corcel\Model\Builder;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class TaxonomyBuilder
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 * @author Yoram de Langen <yoramdelangen@gmail.com>
 */
class TaxonomyBuilder extends Builder
{
    /**
     * Set taxonomy type to category.
     *
     * @return TaxonomyBuilder
     */
    public function category()
    {
        return $this->where('taxonomy', 'category');
    }

    /**
     * Set taxonomy type to nav_menu.
     *
     * @return TaxonomyBuilder
     */
    public function menu()
    {
        return $this->where('taxonomy', 'nav_menu');
    }

    /**
     * @param string $name
     * @return static
     */
    public function name($name)
    {
        return $this->where('taxonomy', $name);
    }

    /**
     * Get a term taxonomy by specific slug.
     *
     * @param string $slug
     * @return TaxonomyBuilder
     */
    public function slug($slug = null)
    {
        if (!is_null($slug) && !empty($slug)) {
            return $this->whereHas('term', function ($query) use ($slug) {
                $query->where('slug', $slug);
            });
        }

        return $this;
    }
}
