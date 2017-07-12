<?php

namespace Corcel;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Class Model
 *
 * @package Corcel
 * @author Mickael Burguet <www.rundef.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Model extends Eloquent
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }

    /**
     * Replace the original hasMany function to forward the connection name.
     *
     * @param string $related
     * @param null   $foreignKey
     * @param null   $localKey
     *
     * @return HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related();
        if ($instance instanceof self) {
            $instance->setConnection($this->getConnection()->getName());
        } else {
            $instance->setConnection($instance->getConnection()->getName());
        }

        $localKey = $localKey ?: $this->getKeyName();

        return new HasMany($instance->newQuery(), $this, $foreignKey, $localKey);
    }

    /**
     * Replace the original hasOne function to forward the connection name.
     *
     * @param string $related
     * @param null   $foreignKey
     * @param null   $localKey
     *
     * @return HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related();
        if ($instance instanceof self) {
            $instance->setConnection($this->getConnection()->getName());
        } else {
            $instance->setConnection($instance->getConnection()->getName());
        }

        $localKey = $localKey ?: $this->getKeyName();

        return new HasOne($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }

    /**
     * Replace the original belongsTo function to forward the connection name.
     *
     * @param string $related
     * @param null   $foreignKey
     * @param null   $otherKey
     * @param null   $relation
     *
     * @return BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
    {
        if (is_null($relation)) {
            list($current, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

            $relation = $caller['function'];
        }

        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relation).'_id';
        }

        $instance = new $related();
        if ($instance instanceof self) {
            $instance->setConnection($this->getConnection()->getName());
        } else {
            $instance->setConnection($instance->getConnection()->getName());
        }

        $query = $instance->newQuery();

        $otherKey = $otherKey ?: $instance->getKeyName();

        return new BelongsTo($query, $this, $foreignKey, $otherKey, $relation);
    }

    /**
     * Replace the original belongsToMany function to forward the connection name.
     *
     * @param string $related
     * @param null   $table
     * @param null   $foreignKey
     * @param null   $otherKey
     * @param null   $relation
     *
     * @return BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
    {
        if (is_null($relation)) {
            $relation = $this->getRelations();
        }

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related();
        if ($instance instanceof self) {
            $instance->setConnection($this->getConnection()->getName());
        } else {
            $instance->setConnection($instance->getConnection()->getName());
        }

        $otherKey = $otherKey ?: $instance->getForeignKey();

        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        $query = $instance->newQuery();

        return new BelongsToMany($query, $this, $table, $foreignKey, $otherKey, $relation);
    }

    /**
     * Get the relation value setting the connection name.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getRelationValue($key)
    {
        $relation = parent::getRelationValue($key);

        if ($relation instanceof Collection) {
            $relation->each(function ($model) {
                $this->setRelationConnection($model);
            });

            return $relation;
        }

        $this->setRelationConnection($relation);

        return $relation;
    }

    /**
     * Set the connection name to model.
     *
     * @param $model
     */
    protected function setRelationConnection($model)
    {
        if ($model instanceof Eloquent) {
            $model->setConnection($this->getConnectionName());
        }
    }

    public function getConnectionName()
    {
        if (!isset($this->connection) && Corcel::isLaravel()) {
            if ($connection = config('corcel.connection')) {
                $this->connection = $connection;
            }
        }

        return $this->connection;
    }
}
