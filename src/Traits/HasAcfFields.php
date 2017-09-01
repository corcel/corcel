<?php
namespace Corcel\Traits;

use Corcel\Acf\AdvancedCustomFields;

/**
 * Trait HasAcfFields
 *
 * @package Corcel\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait HasAcfFields
{
    /**
     * @return AdvancedCustomFields
     */
    public function getAcfAttribute()
    {
        return new AdvancedCustomFields($this);
    }
}
