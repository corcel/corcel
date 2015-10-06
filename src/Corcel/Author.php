<?php

namespace Corcel;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Author model
 *
 * @author Ashwin Sureshkumar<ashwin.sureshkumar@gmail.com>
 */

class Author extends Eloquent {

    protected $table = 'users';
    protected $primaryKey = 'ID';
    protected $hidden = ['user_pass'];


    /**
     * Posts relationship
     *
     * @return Corcel\PostMetaCollection
     */
    public function posts() {

        return $this->hasMany('Corcel\Post', 'post_author');
    }
}
