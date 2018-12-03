<?php

namespace Corcel\Concerns;

/**
 * Trait CustomTimestamps
 *
 * @package Corcel\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait CustomTimestamps
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function setCreatedAt($value)
    {
        $field = static::CREATED_AT;
        $this->{$field} = $value;

        $field .= '_gmt';
        $this->{$field} = $value;

        return parent::setCreatedAt($value);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function setUpdatedAt($value)
    {
        $field = static::UPDATED_AT;
        $this->{$field} = $value;

        $field .= '_gmt';
        $this->{$field} = $value;

        return parent::setUpdatedAt($value);
    }
}
