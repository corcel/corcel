<?php

namespace Corcel;

/**
 * Menu class.
 *
 * @author Yoram de Langen <yoramdelangen@gmail.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Menu extends TermTaxonomy
{
    /**
     * Set taxonomy type.
     *
     * @var string
     */
    protected $taxonomy = 'nav_menu';

    /**
     * Add related relationships we need to use for a menu.
     *
     * @var array
     */
    protected $with = ['term', 'items'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function items()
    {
        return $this->belongsToMany(
            MenuItem::class, 'term_relationships', 'term_taxonomy_id', 'object_id'
        )->orderBy('menu_order');
    }
}
