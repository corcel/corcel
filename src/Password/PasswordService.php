<?php

namespace Corcel\Password;

use Hautelook\Phpass\PasswordHash;

require_once(__DIR__ . "/../../vendor/autoload.php");

class PasswordService
{
    public function __construct()
    {
        $this->hasher = new PasswordHash(8, true);
    }

    /**
     * Create a hash (encrypt) of a plain text password.
     *
     * For integration with other applications, this function can be overwritten to
     * instead use the other package password checking algorithm.
     *
     * @since 2.5.0
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
     * Maintains compatibility between old version and the new cookie authentication
     * protocol using PHPass library. The $hash parameter is the encrypted password
     * and the function compares the plain text password when encrypted similarly
     * against the already encrypted password to see if they match.
     *
     * For integration with other applications, this function can be overwritten to
     * instead use the other package password checking algorithm.
     *
     * @since 2.5.0
     *
     * @param string $password Plaintext user's password
     * @param string $hash Hash of the user's password to check against.
     * @param string|int $user_id Optional. User ID.
     * @return bool False, if the $password does not match the hashed password
     */
    public function check($password, $hash, $user_id = '')
    {
        // If the hash is still md5...
        if (strlen($hash) <= 32) {
            return ($hash === md5($password));
        }
        // If the stored hash is longer than an MD5, presume the
        // new style phpass portable hash.
        return $this->hasher->CheckPassword($password, $hash);
    }
}
