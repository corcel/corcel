<?php

namespace Corcel\Traits;

/**
 * UpdatedAt trait
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait UpdatedAtTrait
{
    public function setUpdatedAt($value)
    {
        $field = static::UPDATED_AT;
        $this->{$field} = $value;

        $field .= '_gmt';
        $this->{$field} = $value;

        return parent::setUpdatedAt($value);
    }
}
