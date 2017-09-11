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
     * @var string
     */
    protected $postType;

    /**
     * Replace the original hasMany function to forward the connection name.
     *
     * @param string $related
     * @param string $foreignKey
     * @param string $localKey
     * @return HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = $this->setInstanceConnection(
            new $related()
        );

        $localKey = $localKey ?: $this->getKeyName();

        return new HasMany($instance->newQuery(), $this, $foreignKey, $localKey);
    }

    /**
     * Replace the original hasOne function to forward the connection name.
     *
     * @param string $related
     * @param string $foreignKey
     * @param string $localKey
     * @return HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = $this->setInstanceConnection(
            new $related()
        );

        $localKey = $localKey ?: $this->getKeyName();

        return new HasOne($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }

    /**
     * Replace the original belongsTo function to forward the connection name.
     *
     * @param string $related
     * @param string $foreignKey
     * @param string $otherKey
     * @param string $relation
     * @return BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
    {
        if (is_null($relation)) {
            list(, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $relation = $caller['function'];
        }

        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relation).'_id';
        }

        $instance = $this->setInstanceConnection(
            new $related()
        );

        $query = $instance->newQuery();

        $otherKey = $otherKey ?: $instance->getKeyName();

        return new BelongsTo($query, $this, $foreignKey, $otherKey, $relation);
    }

    /**
     * Replace the original belongsToMany function to forward the connection name.
     *
     * @param string $related
     * @param string $table
     * @param string $foreignPivotKey
     * @param string $relatedPivotKey
     * @param string $parentKey
     * @param string $relatedKey
     * @param string $relation
     * @return BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null,
                                  $parentKey = null, $relatedKey = null, $relation = null)
    {
        if (is_null($relation)) {
            $relation = $this->guessBelongsToManyRelation();
        }

        $instance = $this->setInstanceConnection(
            $this->newRelatedInstance($related)
        );

        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();

        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();

        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        return new BelongsToMany(
            $instance->newQuery(), $this, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey ?: $this->getKeyName(),
            $relatedKey ?: $instance->getKeyName(), $relation
        );
    }

    /**
     * Get the relation value setting the connection name.
     *
     * @param string $key
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

    /**
     * @return string
     */
    public function getConnectionName()
    {
        if (!isset($this->connection) && Corcel::isLaravel()) {
            if ($connection = config('corcel.connection')) {
                $this->connection = $connection;
            }
        }

        return $this->connection;
    }

    /**
     * @param $instance
     */
    protected function setInstanceConnection($instance)
    {
        return $instance->setConnection(
            $instance instanceof self ?
                $this->getConnection()->getName() :
                $instance->getConnection()->getName()
        );
    }
}
