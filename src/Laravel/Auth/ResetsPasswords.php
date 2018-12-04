<?php

namespace Corcel\Laravel\Auth;

use Auth;
use Corcel\Services\PasswordService;
use Illuminate\Contracts\Auth\CanResetPassword;

/**
 * Trait ResetsPasswords
 *
 * @package Corcel\Laravel\Auth
 * @author Mickael Burguet <www.rundef.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait ResetsPasswords
{
    /**
     * Reset the given user's password.
     *
     * @param CanResetPassword $user
     * @param string $password
     */
    protected function resetPassword(CanResetPassword $user, $password)
    {
        $user->user_pass = (new PasswordService())->makeHash($password);
        $user->save();

        $this->guard()->login($user);
    }
}
