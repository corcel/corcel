<?php

namespace Corcel\Model;

/**
 * Class Menu
 *
 * @package Corcel\Model
 * @author Yoram de Langen <yoramdelangen@gmail.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Menu extends Taxonomy
{
    /**
     * @var string
     */
    protected $taxonomy = 'nav_menu';

    /**
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
