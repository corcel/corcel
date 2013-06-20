<?php 

/**
 * Corcel\PostMetaCollection
 * 
 * @author Junior Grossi <me@juniorgrossi.com>
 */

namespace Corcel;

use Illuminate\Database\Eloquent\Collection;

class PostMetaCollection extends Collection
{
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
}