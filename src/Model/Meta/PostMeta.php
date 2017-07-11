<?php

namespace Corcel\Model\Meta;

use Corcel\Model;
use Corcel\Model\Collection\PostMetaCollection;
use Corcel\Model\Post;
use Exception;

/**
 * Class PostMeta
 *
 * @package Corcel\Model\Meta
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class PostMeta extends Model
{
    /**
     * @var string
     */
    protected $table = 'postmeta';

    /**
     * @var string
     */
    protected $primaryKey = 'meta_id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['meta_key', 'meta_value', 'post_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @var array
     */
    protected $appends = ['value'];

    /**
     * @return mixed
     */
    public function getValueAttribute()
    {
        try {
            $value = unserialize($this->meta_value);
            // if we get false, but the original value is not false then something has gone wrong.
            // return the meta_value as is instead of unserializing
            // added this to handle cases where unserialize doesn't throw an error that is catchable
            return $value === false && $this->meta_value !== false ? $this->meta_value : $value;
        } catch (Exception $ex) {
            return $this->meta_value;
        }
    }

    /**
     * @param string $key
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
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     * @return PostMetaCollection
     */
    public function newCollection(array $models = [])
    {
        return new PostMetaCollection($models);
    }
}
