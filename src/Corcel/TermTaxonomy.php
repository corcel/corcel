<?php

namespace Corcel;

use Illuminate\Database\Eloquent\Model;

class TermTaxonomy extends Model
{
    protected $table = 'wp_term_taxonomy';
    protected $primaryKey = 'term_taxonomy_id';

    public function term()
    {
        return $this->belongsTo('Corcel\Term', 'term_id');
    }

    public function parentTerm()
    {
        return $this->belongsTo('Corcel\TermTaxonomy', 'parent');
    }
}