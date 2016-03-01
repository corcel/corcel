<?php

use Corcel\User;
use Corcel\Providers\AuthUserProvider;
use Corcel\Password\PasswordService;

class AuthenticationTest extends PHPUnit_Framework_TestCase
{
    public function testPasswordService()
    {
        $passwordService = new PasswordService;

        $this->assertTrue($passwordService->wp_check_password('admin', $passwordService->wp_hash_password('admin')));
        $this->assertFalse($passwordService->wp_check_password('admin', $passwordService->wp_hash_password('admin`')));

        $this->assertTrue($passwordService->wp_check_password('rEn2b2N3TX', $passwordService->wp_hash_password('rEn2b2N3TX')));
        $this->assertTrue($passwordService->wp_check_password('+0q?\'t&SBT\'*2VBk7UE(,uj6UG23Us', $passwordService->wp_hash_password('+0q?\'t&SBT\'*2VBk7UE(,uj6UG23Us')));
    }


    public function testUserProvider()
    {
        $userProvider = new AuthUserProvider();
        $passwordService = new PasswordService;

        $user = new User;

        $user->user_pass = $passwordService->wp_hash_password('admin');
        $this->assertTrue($userProvider->validateCredentials($user, ['password' => 'admin']));
        $this->assertFalse($userProvider->validateCredentials($user, ['password' => 'admin`']));

        $user->user_pass = $passwordService->wp_hash_password('(V-._p@q8sK=TK1QYHIi');
        $this->assertTrue($userProvider->validateCredentials($user, ['password' => '(V-._p@q8sK=TK1QYHIi']));
        $this->assertFalse($userProvider->validateCredentials($user, ['password' => '(V-._p@q8sK=TK1QYHIi)`']));

        $user->user_pass = $passwordService->wp_hash_password(')_)E~O79}?w+5"4&6{!;ct>656Lx~5');
        $this->assertTrue($userProvider->validateCredentials($user, ['password' => ')_)E~O79}?w+5"4&6{!;ct>656Lx~5']));
        $this->assertFalse($userProvider->validateCredentials($user, ['password' => ') )E~O79}?w+5"4&6{!;ct>656Lx~5`']));
        
    }
}

