<?php

namespace Corcel\Traits;

use Illuminate\Support\Arr;

/**
 * Trait AliasesTrait
 *
 * @package Corcel\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait AliasesTrait
{
    /**
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($value === null && isset($this->aliases)) {
            if ($value = Arr::get($this->aliases, $key)) {
                if (is_array($value)) {
                    $meta = Arr::get($value, 'meta');

                    return $meta ? $this->meta->$meta : null;
                }

                return parent::getAttribute($value);
            }
        }

        return $value;
    }

    /**
     * @param string $new
     * @param string $old
     */
    public function addAlias($new, $old)
    {
        $this->aliases[$new] = $old;
    }
}
