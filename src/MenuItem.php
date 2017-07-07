<?php

namespace Corcel;

use Illuminate\Support\Arr;

/**
 * Class MenuItem
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class MenuItem extends Post
{
    /**
     * @var string
     */
    protected $postType = 'nav_menu_item';

    /**
     * @var array
     */
    private $instanceRelations = [
        'post' => Post::class,
        'page' => Page::class,
        'custom' => CustomLink::class,
        'category' => TermTaxonomy::class,
    ];

    /**
     * @return Post|Page|CustomLink|TermTaxonomy
     */
    public function instance()
    {
        $className = Arr::get(
            $this->instanceRelations, $this->meta->_menu_item_object
        );

        if ($className) {
            return (new $className)->newQuery()
                ->findOrFail($this->meta->_menu_item_object_id);
        }

        return null;
    }
}
