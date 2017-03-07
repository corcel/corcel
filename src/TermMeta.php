<?php

namespace Corcel;

class TermMeta extends PostMeta
{
    protected $table = 'termmeta';
    protected $fillable = ['meta_key', 'meta_value', 'term_id'];
}
