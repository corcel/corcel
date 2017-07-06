<?php

namespace Corcel\Traits;

/**
 * Trait MetaCollection
 *
 * @package Corcel\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait MetaCollection
{
    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->items) && count($this->items)) {
            $meta = $this->first(function ($meta) use ($key) {
                return $meta->meta_key === $key;
            });

            return $meta ? $meta->meta_value : null;
        }

        return null;
    }
}