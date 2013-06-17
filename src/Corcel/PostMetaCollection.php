<?php 

namespace Corcel;

use Illuminate\Database\Eloquent\Collection;

class PostMetaCollection extends Collection
{
    public function __get($key)
    {
        foreach ($this->items as $item) {
            if ($item->meta_key == $key) {
                return $item->meta_value;
            }
        }
    }
}