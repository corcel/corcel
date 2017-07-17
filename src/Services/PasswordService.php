<?php

namespace Corcel\Services;

use Hautelook\Phpass\PasswordHash;

/**
 * Class PasswordService
 *
 * @package Corcel\Services
 * @author Mickael Burguet <www.rundef.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class PasswordService
{
    public function __construct()
    {
        $this->hasher = new PasswordHash(8, true);
    }

    /**
     * Create a hash (encrypt) of a plain text password.
     *
     * @param string $password Plain text user password to hash
     * @return string The hash string of the password
     */
    public function makeHash($password)
    {
        return $this->hasher->HashPassword(trim($password));
    }

    /**
     * Checks the plaintext password against the encrypted Password.
     *
     * @param string $password Plaintext password
     * @param string $hash Hashed password
     * @return bool
     */
    public function check($password, $hash)
    {
        if (strlen($hash) <= 32) { // if the hash is still md5
            return $hash === md5($password);
        }

        return $this->hasher->CheckPassword($password, $hash);
    }
}
