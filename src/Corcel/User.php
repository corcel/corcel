<?php

/**
 * User model
 *
 * @author Ashwin Sureshkumar<ashwin.sureshkumar@gmail.com>
 * @author Mickael Burguet <www.rundef.com>
 */

namespace Corcel;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Eloquent
{
    const CREATED_AT = 'user_registered';
    const UPDATED_AT = 'updated_at';

    protected $table = 'users';
    protected $primaryKey = 'ID';
    protected $hidden = ['user_pass'];
    protected $dates = ['user_registered'];
    protected $with = array('meta');


    // Disable updated_at
    public function setUpdatedAtAttribute($value)
    {
    }

    /**
     * Meta data relationship
     *
     * @return Corcel\UserMetaCollection
     */
    public function meta()
    {
        return $this->hasMany('Corcel\UserMeta', 'user_id');
    }

    public function fields()
    {
        return $this->meta();
    }


    /**
     * Posts relationship
     *
     * @return Corcel\PostMetaCollection
     */
    public function posts() {

        return $this->hasMany('Corcel\Post', 'post_author');
    }

   
    /**
     * Comments relationship
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function comments()
    {
        return $this->hasMany('Corcel\Comment', 'user_id');
    }

    /**
     * Overriding newQuery() to the custom UserBuilder with some interesting methods
     *
     * @param bool $excludeDeleted
     * @return Corcel\UserBuilder
     */
    public function newQuery()
    {
        $builder = new UserBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);
        $builder->orderBy('user_registered', 'desc');

        return $builder;
    }

    /**
     * Magic method to return the meta data like the user original fields
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