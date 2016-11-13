<?php

/**
 * Corcel\PostMetaCollection.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */

namespace Corcel;

use Illuminate\Database\Eloquent\Collection;

class PostMetaCollection extends Collection
{
    protected $changedKeys = [];

    /**
     * Search for the desired key and return only the row that represent it.
     *
     * @param string $key
     *
     * @return string
     */
    public function __get($key)
    {
        foreach ($this->items as $item) {
            if ($item->meta_key == $key) {
                return $item->value;
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

        $item = new PostMeta([
            'meta_key' => $key,
            'meta_value' => $value,
        ]);

        $this->push($item);
    }

    public function save($postId)
    {
        $this->each(function ($item) use ($postId) {
            if (in_array($item->meta_key, $this->changedKeys)) {
                $item->post_id = $postId;
                $item->save();
            }
        });
    }
}
