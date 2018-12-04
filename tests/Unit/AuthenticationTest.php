<?php

namespace Corcel\Tests\Unit;

use Corcel\Laravel\Auth\AuthUserProvider;
use Corcel\Model\User;
use Corcel\Services\PasswordService;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthenticationTest
 *
 * @author Mickael Burguet <www.rundef.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class AuthenticationTest extends \Corcel\Tests\TestCase
{
    /** @var PasswordService */
    protected $checker;

    /** @var AuthUserProvider */
    protected $provider;

    public function setUp(): void
    {
        parent::setUp();
        $this->checker = new PasswordService();
        $this->provider = new AuthUserProvider();
    }

    public function test_it_can_check_passwords()
    {
        $this->assertTrue($this->checker->check('admin', $this->checker->makeHash('admin')));
        $this->assertTrue($this->checker->check('admin', '$P$BrYiES.08ardK6pQme0LdlmQ0idrIe/'));
        $this->assertTrue($this->checker->check('rEn2b2N3TX', $this->checker->makeHash('rEn2b2N3TX')));

        $this->assertTrue(
            $this->checker->check(
                '+0q?\'t&SBT\'*2VBk7UE(,uj6UG23Us',
                $this->checker->makeHash('+0q?\'t&SBT\'*2VBk7UE(,uj6UG23Us')
            )
        );
    }

    public function test_it_can_validate_simple_passwords()
    {
        $user = factory(User::class)->make([
            'user_pass' => $this->checker->makeHash('foobar'),
        ]);

        $this->assertTrue($this->provider->validateCredentials($user, ['password' => 'foobar']));
        $this->assertFalse($this->provider->validateCredentials($user, ['password' => 'foobaz']));
    }

    public function test_it_can_validate_complex_passwords()
    {
        $password = ')_)E~O79}?w+5"4&6{!;ct>656Lx~5';

        $user = factory(User::class)->make([
            'user_pass' => $this->checker->makeHash($password),
        ]);

        $this->assertTrue($this->provider->validateCredentials($user, compact('password')));
        $this->assertFalse($this->provider->validateCredentials($user, ['password' => $password.'a']));
    }

    public function test_it_can_authenticate_users_using_auth_facade_with_email()
    {
        factory(User::class)->create([
            'user_pass' => $this->checker->makeHash('correct-password'),
        ]);

        $this->assertTrue(Auth::validate([
            'email' => 'admin@example.com',
            'password' => 'correct-password',
        ]));

        $this->assertFalse(Auth::validate([
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]));
    }

    public function test_it_can_authenticate_users_using_auth_facade_with_username()
    {
        factory(User::class)->create([
            'user_pass' => $this->checker->makeHash('correct-password'),
        ]);

        $this->assertTrue(Auth::validate([
            'username' => 'admin',
            'password' => 'correct-password',
        ]));

        $this->assertFalse(Auth::validate([
            'username' => 'admin',
            'password' => 'wrong-password',
        ]));
    }
}
