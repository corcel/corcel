<?php

namespace Corcel\Tests\Unit\Laravel\Auth;

use Corcel\Laravel\Auth\AuthUserProvider;
use Corcel\Model\User;
use Corcel\Tests\TestCase;

class AuthUserProviderTest extends TestCase
{
    public function test_it_can_retrieve_users_by_id()
    {
        $user = factory(User::class)->create();

        $provider = new AuthUserProvider();
        $new_user = $provider->retrieveById($user->ID);

        $this->assertEquals($user->fresh(), $new_user);
    }

    public function test_it_can_retrieve_users_by_token()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->saveMeta('remember_token', $token = str_random());

        $provider = new AuthUserProvider();
        $new_user = $provider->retrieveByToken($user->ID, $token);
        
        $this->assertEquals($user->fresh(), $new_user);
    }

    public function test_it_can_update_remember_token()
    {
        $user = factory(User::class)->create();
        $provider = new AuthUserProvider();

        $provider->updateRememberToken($user, $token = str_random());
        $new_user = $provider->retrieveByToken($user->ID, $token);

        $this->assertEquals($user->fresh(), $new_user);
    }

    public function test_it_returns_null_if_credentials_do_not_match()
    {
        $provider = new AuthUserProvider();

        $user = $provider->retrieveByCredentials(['foo' => 'bar']);

        $this->assertNull($user);
    }

    public function test_it_returns_false_if_there_is_no_password_on_validation()
    {
        $user = factory(User::class)->create();

        $provider = new AuthUserProvider();

        $this->assertFalse($provider->validateCredentials($user, ['username' => $user->username]));
    }
}
