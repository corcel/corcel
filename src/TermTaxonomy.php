<?php

namespace Corcel;

class TermTaxonomy extends Model
{
    protected $table = 'term_taxonomy';
    protected $primaryKey = 'term_taxonomy_id';
    protected $with = ['term'];
    public $timestamps = false;

    /**
     * Relationship with Term model
     * @return Illuminate\Database\Eloquent\Relations
     */
    public function term()
    {
        return $this->belongsTo('Corcel\Term', 'term_id');
    }

    /**
     * Relationship with parent Term model
     * @return Illuminate\Database\Eloquent\Relations
     */
    public function parentTerm()
    {
        return $this->belongsTo('Corcel\TermTaxonomy', 'parent');
    }

    /**
     * Relationship with Posts model
     * @return Illuminate\Database\Eloquent\Relations
     */
    public function posts()
    {
        return $this->belongsToMany('Corcel\Post', 'term_relationships', 'term_taxonomy_id', 'object_id');
    }


    /**
     * Alias from posts, but made quering nav_items cleaner.
     * Also only possible to use when Menu model is called or taxonomy is 'nav_menu'
     *
     * @return Illuminate\Database\Eloquent\Relations
     */
    public function nav_items()
    {
        if ($this->taxonomy == 'nav_menu') {
            return $this->posts()->orderBy('menu_order');
        }

        return $this;
    }

    /**
     * Overriding newQuery() to the custom TermTaxonomyBuilder with some interesting methods
     * @param bool $excludeDeleted
     * @return Corcel\TermTaxonomyBuilder
     */
    public function newQuery($excludeDeleted = true)
    {
        $builder = new TermTaxonomyBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);

        if (isset($this->taxonomy) and !empty($this->taxonomy) and !is_null($this->taxonomy)) {
            $builder->where('taxonomy', $this->taxonomy);
        }

        return $builder;
    }

    /**
     * Magic method to return the meta data like the post original fields
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

    public function __isset($key)
    {
        if (property_exists($this, $key)) {
            return true;
        }

        if (property_exists($this, 'term') && property_exists($this->term, $key)) {
            return true;
        }

        return parent::__isset($key);
    }
}
