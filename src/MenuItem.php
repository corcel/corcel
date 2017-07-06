<?php

namespace Corcel;

/**
 * Class MenuItem
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class MenuItem extends Post
{
    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        $value = parent::__get($key);
        // TODO
        if (isset($this->$key) and empty($this->$key)) {
            // fix for menu items when chosing category to show
            if (in_array($key, ['post_title', 'post_name'])) {
                $type = $this->meta->_menu_item_object;
                $taxonomy = null;

                // Support certain types of meta objects
                if ($type == 'category') {
                    $taxonomy = $this->meta()->where('meta_key', '_menu_item_object_id')
                        ->first()->taxonomy('meta_value')->first();
                } elseif ($type == 'post_tag') {
                    $taxonomy = $this->meta()->where('meta_key', '_menu_item_object_id')
                        ->first()->taxonomy('meta_value')->first();
                } elseif ($type == 'post') {
                    $post = $this->meta()->where('meta_key', '_menu_item_object_id')
                        ->first()->post(true)->first();

                    return $post->$key;
                }

                // TODO Extract this
                if (isset($taxonomy) && $taxonomy->exists) {
                    if ($key == 'post_title') {
                        return $taxonomy->name;
                    } elseif ($key == 'post_name') {
                        return $taxonomy->slug;
                    }
                }
            }
        }
    }
}
