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
            return $this->filter(function ($meta) use ($key) {
                return $meta->meta_key === $key;
            })->first()->meta_value;
        }

        return parent::__get($key);
    }
}