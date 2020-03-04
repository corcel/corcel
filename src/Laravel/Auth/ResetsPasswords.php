<?php

namespace Corcel\Laravel\Auth;

use Corcel\Services\PasswordService;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Auth;

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

    /**
     * @return mixed
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
