<?php

/**
 * Corcel\PostMetaCollection
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */

namespace Corcel;

use Illuminate\Database\Eloquent\Collection;

class PostMetaCollection extends Collection
{
    protected $changedKeys = array();

    /**
     * Search for the desired key and return only the row that represent it
     *
     * @param string $key
     * @return string
     */
    public function __get($key)
    {
        foreach ($this->items as $item) {
            if ($item->meta_key == $key) {
                return $item->meta_value;
            }
        }
    }

    public function __set($key, $value)
    {
        $this->changedKeys[] = $key;

        foreach ($this->items as $item) {
            if ($item->meta_key == $key) {
                $item->meta_value = $value;
                return;
            }
        }

        $item = new PostMeta(array(
            'meta_key' => $key,
            'meta_value' => $value,
        ));

        $this->push($item);
    }

    public function save($postId)
    {
        $this->each(function($item) use ($postId) {
            if (in_array($item->meta_key, $this->changedKeys)) {
                $item->post_id = $postId;
                $item->save();
            }
        });
    }

    /**
     * Easy why to get a PostMeta object from the collection.
     *
     * @param  string $column
     * @param  mixed $value
     * @return Corcel\PostMeta
     */
    public function where($column, $value)
    {
        foreach ($this->items as $item) {
            // check if the column name exists in clause
            if (!isset($item->{$column}))
                return false;

            // check if the values match up
            if ($item->{$column} == $value)
                return $item;
        }
        return false;
    }

}