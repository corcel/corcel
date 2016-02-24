<?php 

namespace Corcel\Password;

use Hautelook\Phpass\PasswordHash;

class Encrypter
{
    function __construct()
    {
        $this->hasher = new PasswordHash(8, true);
    }
  

    public function make($password)
    {
        return $this->hasher->HashPassword(trim($password));
    }
   

    public function check($password, $hash)
    {
        return (strlen($hash) <= 32 ? ($hash == md5($password)) : $this->hasher->CheckPassword($password, $hash));
    }
}