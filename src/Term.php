<?php

namespace Corcel;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Term extends Eloquent
{
    protected $table = 'terms';
    protected $primaryKey = 'term_id';
    public $timestamps = false;
}