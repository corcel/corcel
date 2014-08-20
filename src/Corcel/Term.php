<?php

namespace Corcel;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Term extends Eloquent
{
    protected $table = 'wp_terms';
    protected $primaryKey = 'term_id';


}