<?php

namespace Corcel\Tests\Unit\Laravel\Auth;

use Corcel\Laravel\Auth\ResetsPasswords as CorcelResetsPasswords;
use Corcel\Model\User;
use Corcel\Services\PasswordService;
use Corcel\Tests\TestCase;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Routing\Controller;

class ResetPasswordsTest extends TestCase
{
    public function test_it_resets_password()
    {
        $user = factory(User::class)->create();
        $fake_class = new FakeController();

        $method = new \ReflectionMethod($fake_class, 'resetPassword');
        $method->setAccessible(true);
        $method->invoke($fake_class, $user, 'bar');

        $this->assertTrue((new PasswordService())->check('bar', $user->user_pass));
    }
}

class FakeController extends Controller
{
    use ResetsPasswords, CorcelResetsPasswords {
        CorcelResetsPasswords::resetPassword insteadof ResetsPasswords;
    }
}
