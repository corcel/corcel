<?php

namespace Corcel\Traits;

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

        if ($value === null) {
            if (isset($this->aliases) && isset($this->aliases[$key])) {
                $value = $this->aliases[$key];

                if (is_array($value)) {
                    foreach ($value as $key => $val) {
                        return $this->$key->$val;
                    }
                }

                return parent::getAttribute($value);
            }
        }

        return $value;
    }
}
