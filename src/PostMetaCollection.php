<?php

namespace Corcel;

use Corcel\Traits\MetaCollection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PostMetaCollection
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class PostMetaCollection extends Collection
{
    use MetaCollection;

    /**
     * @var array
     */
    protected $changedKeys = [];

    /**
     * @param $key
     * @param $value
     *
     * TODO remove this
     * @see UserMetaCollection
     */
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

    /**
     * @param $postId
     * TODO remove this
     * @see UserMetaCollection
     */
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
