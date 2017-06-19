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
        if (isset($this->aliases) && isset($this->aliases[$key])) {
            return parent::getAttribute(
                $this->aliases[$key]
            );
        }

        return parent::getAttribute($key);
    }
}
