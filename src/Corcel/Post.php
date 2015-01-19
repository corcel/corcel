<?php

/**
 * Post model
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */

namespace Corcel;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Eloquent
{
    const CREATED_AT = 'post_date';
    const UPDATED_AT = 'post_modified';

    protected $table = 'posts';
    protected $primaryKey = 'ID';
    protected $with = array('meta');

    /**
     * Meta data relationship
     *
     * @return Corcel\PostMetaCollection
     */
    public function meta()
    {
        return $this->hasMany('Corcel\PostMeta', 'post_id');
    }

    /**
     * Return all meta fields
     *
     * @return Corcel\PostMetaCollection
     */
    public function fields()
    {
        return $this->meta();
    }

    /**
     * Get parent post of current post
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function parent()
    {
        return $this->hasOne('Corcel\Post', 'ID', 'post_parent');
    }

    /**
     * Taxonomy relationship
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function taxonomies()
    {
        return $this->belongsToMany('Corcel\TermTaxonomy', 'term_relationships', 'object_id', 'term_taxonomy_id');
    }

    /**
     * Comments relationship
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function comments()
    {
        return $this->hasMany('Corcel\Comment', 'comment_post_ID');
    }

    /**
     * Get attachment
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function attachment()
    {
        return $this->hasOne('Corcel\Post', 'post_parent')->type('attachment');
    }


    /**
     * Get revisions from post
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function revision()
    {
        return $this->hasMany('Corcel\Post', 'post_parent')->type('revision');
    }

    /**
     * Check if the current Post/Page has a attachment
     *
     * @return boolean
     */
    public function hasAttachment()
    {
        return (bool) $this->attachment && (bool)$this->getModel()->attachment->exists;
    }

    /**
     * Get URL from post/page/attachment given by 'guid'. When $clean variable
     * is true, the returned url will look clean and slug-wise (included category if is).
     *
     * @return string
     */
    public function url($clean=false)
    {
        if( $this->post_type == 'attachment' && $clean)

            return $this->meta->_wp_attached_file;

        else if( in_array($this->post_type, [ 'post', 'page' ]) && $clean)

            if( (int) $this->post_parent > 0 )
                return '/'. $this->parent()->first()->post_name .'/'. $this->post_name;
            else
                return '/'. $this->post_name;

        else
            return $this->guid;
    }

    /**
     * Overriding newQuery() to the custom PostBuilder with some interesting methods
     *
     * @param bool $excludeDeleted
     * @return Corcel\PostBuilder
     */
    public function newQuery($excludeDeleted = true)
    {
        $builder = new PostBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);
        $builder->orderBy('post_date', 'desc');

        if (isset($this->postType) and $this->postType) {
            $builder->type($this->postType);
        }

        if ($excludeDeleted and $this->softDelete) {
            $builder->whereNull($this->getQualifiedDeletedAtColumn());
        }

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
            if (isset($this->meta()->get()->$key)) {
                return $this->meta()->get()->$key;
            }
        }

        return parent::__get($key);
    }

    public function save(array $options = array())
    {
        if (isset($this->attributes[$this->primaryKey])) {
            $this->meta->save($this->attributes[$this->primaryKey]);
        }

        return parent::save($options);
    }

    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related;
        $instance->setConnection($this->getConnection()->getName());

        $localKey = $localKey ?: $this->getKeyName();

        return new HasMany($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }

    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
    {
        if (is_null($relation))
        {
            $relation = $this->getBelongsToManyCaller();
        }

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related;
        $instance->setConnection($this->getConnection()->getName());

        $otherKey = $otherKey ?: $instance->getForeignKey();

        if (is_null($table))
        {
            $table = $this->joiningTable($related);
        }

        $query = $instance->newQuery();

        return new BelongsToMany($query, $this, $table, $foreignKey, $otherKey, $relation);
    }

}