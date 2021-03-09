<?php

namespace Corcel\Concerns;

use Corcel\Concerns\MetaFields;

/**
 * Trait HasRoles
 * @package App\Concerns
 */
trait HasRoles
{
    use MetaFields;

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole($role = '')
    {
        return in_array($role, $this->capabilities);
    }

    /**
     * @param array $roles
     * @return bool
     */
    public function hasAnyRoles($roles = [])
    {
        if (empty($roles)) {
            return false;
        }
        
       foreach ($roles as $role) {
           if ($this->hasRole($role)) {
                return true;
           }
        }
        
        return false;
    }

    /**
     * @return array
     */
    public function getCapabilitiesAttribute()
    {
        return array_keys($this->getMeta('wp_capabilities'));
    }

    /**
     * @return bool
     */
    public function getIsAdminAttribute()
    {
        return $this->hasRole('administrator');
    }
}
