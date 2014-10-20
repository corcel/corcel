<?php

namespace Corcel;

use Illuminate\Database\Eloquent\Model;

class TermTaxonomy extends Model
{
    protected $table = 'term_taxonomy';
    protected $primaryKey = 'term_taxonomy_id';
    protected $with = array('term');

    public function term()
    {
        return $this->belongsTo('Corcel\Term', 'term_id');
    }

    public function parentTerm()
    {
        return $this->belongsTo('Corcel\TermTaxonomy', 'parent');
    }

    public function posts()
    {
        return $this->belongsToMany('Corcel\Post', 'term_relationships', 'term_taxonomy_id', 'object_id');
    }

    /**
     * Overriding newQuery() to the custom TermTaxonomyBuilder with some interesting methods
     *
     * @param bool $excludeDeleted
     * @return Corcel\TermTaxonomyBuilder
     */
    public function newQuery($excludeDeleted = true)
    {
        $builder = new TermTaxonomyBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);

        if( isset($this->taxonomy) and !empty($this->taxonomy) and !is_null($this->taxonomy) )
            $builder->where('taxonomy', $this->taxonomy);

        return $builder;
    }

    /**
     * Magic method to return the meta data like the post original fields
     *
     * @param string $key
     * @return string
     */
    public function __get($key)
    {
        if (!isset($this->$key)) {
            if (isset($this->term->$key)) {
                return $this->term->$key;
            }
        }

        return parent::__get($key);
    }
}