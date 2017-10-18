<?php

namespace Corcel\Services;

use Corcel\Model\Option;
use Illuminate\Support\Arr;

class RoleManager
{
    /**
     * @var string
     */
    protected $optionKey = 'wp_user_roles';

    /**
     * @var array
     */
    protected $option = [];

    /**
     * @var array
     */
    protected $capabilities = [];

    /**
     * @param string $role
     * @return $this
     */
    public function from($role)
    {
        $this->option = Option::get($this->optionKey);
        $role = Arr::get($this->option, $role);
        $this->capabilities = Arr::get($role, 'capabilities');

        return $this;
    }

    /**
     * @param string $name
     * @param array $capabilities
     * @return array
     */
    public function create($name, array $capabilities)
    {
        $key = str_slug($name, '_');

        Option::save($key, array_merge(
            $this->capabilities, $capabilities
        ));

        return Option::get(
            Arr::get(Option::get($this->optionKey), $name)
        );
    }
}

