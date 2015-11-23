<?php

/**
 * Corcel\PostMeta
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */

namespace Corcel;

use Illuminate\Database\Eloquent\Model as Eloquent;

class PostMeta extends Eloquent
{
    protected $table = 'postmeta';
    protected $primaryKey = 'meta_id';
    public $timestamps = false;
    protected $fillable = ['meta_key', 'meta_value', 'post_id'];

    /**
     * Post relationship
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post($ref = false)
    {
        if ($ref) {
            $this->primaryKey = 'meta_value';

            return $this->hasOne('Corcel\Post', 'ID');
        }

        return $this->belongsTo('Corcel\Post');
    }

    /**
     * Taxonomy relationship from the meta_value.
     * @param  string $key
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function taxonomy($primary = null, $where = null)
    {
        // possible to exclude a relationship connection
        if (!is_null($primary) && !empty($primary)) {
            $this->primaryKey = $primary;
        }

        // load relationship
        $relation = $this->hasOne('Corcel\TermTaxonomy', 'term_taxonomy_id');

        // do we need to filter which value to look for with meta_value
        // if (!is_null($where) && !empty($where)) {
        //     $relation->where($where, $this->meta_value);
        // }

        return $relation;
    }

    /**
     * Override newCollection() to return a custom collection
     * @param array $models
     * @return \Corcel\PostMetaCollection
     */
    public function newCollection(array $models = array())
    {
        return new PostMetaCollection($models);
    }
}