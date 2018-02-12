<?php

namespace Corcel\Model;

use Illuminate\Support\Arr;

/**
 * Class MenuItem
 *
 * @package Corcel\Model
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
        'category' => Taxonomy::class,
    ];

    /**
     * @return Post|Page|CustomLink|Taxonomy
     */
    public function object()
    {
        return $this->morphTo();
    }

    public function getObjectIdAttribute()
    {
        return $this->meta->_menu_item_object_id;
    }

    public function getObjectTypeAttribute()
    {
        return $this->meta->_menu_item_object;
    }

    /**
     * Get the parent menu item (if any)
     * 
     * @return MenuItem|null
     */
    public function parent_item()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getParentItemIdAttribute()
    {
        return $this->meta->_menu_item_menu_item_parent;
    }

    /**
     * @deprecated in favor of parent_item()->object
     */
    public function parent()
    {
        return $this->parent_item->object ?? null;
    }

    /**
     * @deprecated deprecated in favor of object()
     * @return Post|Page|CustomLink|Taxonomy
     */
    public function instance()
    {
        if ($className = $this->getClassName()) {
            return $this->object;
        }

        return null;
    }

    /**
     * @return string
     */
    private function getClassName()
    {
        return Arr::get(
            $this->instanceRelations, $this->meta->_menu_item_object
        );
    }
}
