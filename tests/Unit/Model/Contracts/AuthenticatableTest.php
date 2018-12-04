<?php

namespace Corcel\Tests\Unit\Model\Contracts;

use Corcel\Model\User;
use Corcel\Tests\TestCase;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class AuthenticatableTest
 *
 * @package Corcel\Tests\Unit\Model\Contracts
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class AuthenticatableTest extends TestCase
{
    public function test_get_auth_identifier_name()
    {
        /** @var Authenticatable $user */
        $user = factory(User::class)->create();
        $this->assertEquals('ID', $user->getAuthIdentifierName());
    }

    public function test_get_auth_identifier()
    {
        /** @var Authenticatable $user */
        $user = factory(User::class)->create();
        $this->assertEquals($user->ID, $user->getAuthIdentifier());
    }

    public function test_get_auth_password()
    {
        /** @var Authenticatable $user */
        $user = factory(User::class)->create(['user_pass' => 'secret']);
        $this->assertEquals('secret', $user->getAuthPassword());
    }

    public function test_get_remember_token()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->saveField('remember_token', 'foo');
        $this->assertEquals('foo', $user->getRememberToken());
    }
}
