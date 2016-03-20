<?php

namespace Corcel\Traits;

/**
 * CreatedAt trait
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait CreatedAtTrait 
{
    public function setCreatedAt($value)
    {
        $field = static::CREATED_AT;
        $this->{$field} = $value;

        $field .= '_gmt';
        $this->{$field} = $value;

        return parent::setCreatedAt($value);
    }
}