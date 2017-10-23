<?php

namespace Corcel\Services;

use Corcel\Model\Option;
use Illuminate\Support\Arr;

/**
 * Class RoleManager
 *
 * @package Corcel\Services
 * @author Junior Grossi <juniorgro@gmail.com>
 * @todo Update, delete and list roles
 */
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
     * Create a new RoleManager instance
     */
    public function __construct()
    {
        $this->option = $this->fetch();
    }

    /**
     * @param string $role
     * @return $this
     */
    public function from($role)
    {
        $this->capabilities = Arr::get(
            $this->get($role), 'capabilities'
        );

        return $this;
    }

    /**
     * @param string $role
     * @return array
     */
    public function get($role)
    {
        return Arr::get($this->option->value, $role);
    }

    /**
     * @param string $name
     * @param array $capabilities
     * @return array
     */
    public function create($name, array $capabilities)
    {
        $key = str_slug($name, '_');
        $roles = $this->option->value;

        $roles[$key] = $role = [
            'name' => $name,
            'capabilities' => array_merge($this->capabilities, $capabilities),
        ];

        $this->option->option_value = serialize($roles);

        return $this->option->save() ? $role : null;
    }

    /**
     * @return Option|\Illuminate\Database\Eloquent\Model|null|static
     */
    private function fetch()
    {
        return Option::query()->where([
            'option_name' => $this->optionKey,
        ])->first();
    }
}
