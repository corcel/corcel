<?php

namespace Corcel;

use Illuminate\Database\Eloquent\Model;

class TermRelationship extends Model
{
    protected $table = 'term_relationships';
    protected $primaryKey = array('object_id', 'term_taxonomy_id');

    public function post()
    {
        return $this->belongsTo('Corcel\Post', 'object_id');
    }

    public function taxonomy()
    {
        return $this->belongsTo('Corcel\TermTaxonomy', 'term_taxonomy_id');
    }
}