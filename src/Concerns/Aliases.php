<?php

namespace Corcel\Concerns;

use Illuminate\Support\Arr;

/**
 * Trait AliasesTrait
 *
 * @package Corcel\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait Aliases
{
    /**
     * @return array
     */
    public static function getAliases()
    {
        if (isset(parent::$aliases) && count(parent::$aliases)) {
            return array_merge(parent::$aliases, static::$aliases);
        }

        return static::$aliases;
    }

    /**
     * @param string $new
     * @param string $old
     */
    public static function addAlias($new, $old)
    {
        static::$aliases[$new] = $old;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($value === null && count(static::getAliases())) {
            if ($value = Arr::get(static::getAliases(), $key)) {
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
     * Get alias value from mutator or directly from attribute
     *
     * @param  string $key
     * @param  mixed $value
     * @return mixed
     */
    public function mutateAttribute($key, $value)
    {
        if ($this->hasGetMutator($key)) {
            return parent::mutateAttribute($key, $value);
        }

        return $this->getAttribute($key);
    }
}
