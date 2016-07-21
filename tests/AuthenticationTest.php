<?php

use Corcel\User;
use Corcel\Providers\AuthUserProvider;
use Corcel\Password\PasswordService;

class AuthenticationTest extends PHPUnit_Framework_TestCase
{
    public function testPasswordService()
    {
        $checker = new PasswordService;

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


    public function testUserProvider()
    {
        $userProvider = new AuthUserProvider(null);
        $passwordService = new PasswordService;

        $user = new User;

        $user->user_pass = $passwordService->makeHash('admin');
        $this->assertTrue($userProvider->validateCredentials($user, ['password' => 'admin']));
        $this->assertFalse($userProvider->validateCredentials($user, ['password' => 'admin`']));

        $user->user_pass = $passwordService->makeHash('(V-._p@q8sK=TK1QYHIi');
        $this->assertTrue($userProvider->validateCredentials($user, ['password' => '(V-._p@q8sK=TK1QYHIi']));
        $this->assertFalse($userProvider->validateCredentials($user, ['password' => '(V-._p@q8sK=TK1QYHIi)`']));

        $user->user_pass = $passwordService->makeHash(')_)E~O79}?w+5"4&6{!;ct>656Lx~5');
        $this->assertTrue(
            $userProvider->validateCredentials($user, ['password' => ')_)E~O79}?w+5"4&6{!;ct>656Lx~5'])
        );
        $this->assertFalse(
            $userProvider->validateCredentials($user, ['password' => ') )E~O79}?w+5"4&6{!;ct>656Lx~5`'])
        );
    }
}
