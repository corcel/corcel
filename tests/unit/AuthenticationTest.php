<?php

use Corcel\User;
use Corcel\Providers\AuthUserProvider;
use Corcel\Password\PasswordService;

/**
 * Class AuthenticationTest
 *
 * @author Mickael Burguet <www.rundef.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class AuthenticationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function check_password()
    {
        $checker = new PasswordService();

        $this->assertTrue($checker->check('admin', $checker->makeHash('admin')));
        $this->assertTrue($checker->check('admin', '$P$BrYiES.08ardK6pQme0LdlmQ0idrIe/'));
        $this->assertTrue($checker->check('rEn2b2N3TX', $checker->makeHash('rEn2b2N3TX')));

        $this->assertTrue(
            $checker->check(
                '+0q?\'t&SBT\'*2VBk7UE(,uj6UG23Us',
                $checker->makeHash('+0q?\'t&SBT\'*2VBk7UE(,uj6UG23Us')
            )
        );
    }

    /**
     * @test
     */
    public function user_provider_with_simple_password()
    {
        $provider = new AuthUserProvider(null);
        $service = new PasswordService();

        $user = new User();
        $user->user_pass = $service->makeHash('foobar');

        $this->assertTrue($provider->validateCredentials($user, ['password' => 'foobar']));
        $this->assertFalse($provider->validateCredentials($user, ['password' => 'foobaz']));
    }

    /**
     * @test
     */
    public function user_provider_with_complex_password()
    {
        $provider = new AuthUserProvider(null);
        $service = new PasswordService();
        $password = ')_)E~O79}?w+5"4&6{!;ct>656Lx~5';

        $user = new User();
        $user->user_pass = $service->makeHash($password);

        $this->assertTrue($provider->validateCredentials($user, compact('password')));
        $this->assertFalse($provider->validateCredentials($user, ['password' => $password.'a']));
    }
}
