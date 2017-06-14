<?php

namespace Corcel;

/**
 * Class TermTaxonomy
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class TermTaxonomy extends Model
{
    /**
     * @var string
     */
    protected $table = 'term_taxonomy';

    /**
     * @var string
     */
    protected $primaryKey = 'term_taxonomy_id';

    /**
     * @var array
     */
    protected $with = ['term'];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany(TermMeta::class, 'term_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentTerm()
    {
        return $this->belongsTo(TermTaxonomy::class, 'parent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(
            Post::class, 'term_relationships', 'term_taxonomy_id', 'object_id'
        );
    }

    /**
     * Alias from posts, but made quering nav_items cleaner.
     * Also only possible to use when Menu model is called or taxonomy is 'nav_menu'.
     *
     * @return Illuminate\Database\Eloquent\Relations
     * @todo Fix this
     */
    public function nav_items()
    {
        if ($this->taxonomy == 'nav_menu') {
            return $this->posts()->orderBy('menu_order');
        }

        return $this;
    }

    /**
     * Overriding newQuery() to the custom TermTaxonomyBuilder with some interesting methods.
     *
     * @param bool $excludeDeleted
     * @return TermTaxonomyBuilder
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
     * Magic method to return the meta data like the post original fields.
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
