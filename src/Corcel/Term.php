<?php

namespace Corcel;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Term extends BaseModel
{
    protected $table = 'terms';
    protected $primaryKey = 'term_id';
}