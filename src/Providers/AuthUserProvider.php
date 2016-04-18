<?php

namespace Corcel\Providers;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Corcel\Password\PasswordService;
use Corcel\User;

/**
 * @author Mickael Burguet <www.rundef.com>
 */
class AuthUserProvider implements UserProvider
{
	/**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return User::where('ID', $identifier)->first();
    }



    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed   $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return User::whereId($identifier)->hasMeta('remember_token', $token)->first();
    }



    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $user->save();
    }



    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $user = null;

        if(isset($credentials['username']))
            $user = User::whereUserLogin($credentials['username'])->first();
        else if(isset($credentials['email']))
            $user = User::whereUserEmail($credentials['email'])->first();

       return $user;
    }



    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if(!isset($credentials['password'])) {
            return false;
        }

        $passwordService = new PasswordService;
        
        return $passwordService->check($credentials['password'], $user->user_pass);
    }
}
