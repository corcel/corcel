<?php

/**
 * Base model.
 *
 * @author Mickael Burguet <www.rundef.com>
 */
namespace Corcel;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Model extends Eloquent
{
    /**
     * @param string $related
     * @param null   $foreignKey
     * @param null   $localKey
     * @param bool   $applyConnection  defaults to true. Set to false if you don't want
     *                                 the connection of the calling model to be applied to the related model
     * @return HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null, $applyConnection = true)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related();

        if ($applyConnection) {
            $instance->setConnection($this->getConnection()->getName());
        }

        $localKey = $localKey ?: $this->getKeyName();

        return new HasMany($instance->newQuery(), $this, $foreignKey, $localKey);
    }

    /**
     * @param string $related
     * @param null   $foreignKey
     * @param null   $otherKey
     * @param null   $relation
     * @param bool   $applyConnection  defaults to true. Set to false if you don't want
     *                                 the connection of the calling model to be applied to the related model
     * @return BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null, $applyConnection = true)
    {
        if (is_null($relation)) {
            list($current, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

            $relation = $caller['function'];
        }

        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relation).'_id';
        }

        $instance = new $related();

        if ($applyConnection) {
            $instance->setConnection($this->getConnection()->getName());
        }

        $query = $instance->newQuery();

        $otherKey = $otherKey ?: $instance->getKeyName();

        return new BelongsTo($query, $this, $foreignKey, $otherKey, $relation);
    }

    /**
     * @param string $related
     * @param null   $table
     * @param null   $foreignKey
     * @param null   $otherKey
     * @param null   $relation
     * @param bool   $applyConnection  defaults to true. Set to false if you don't want
     *                                 the connection of the calling model to be applied to the related model
     * @return BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null, $applyConnection = true)
    {
        if (is_null($relation)) {
            $relation = $this->getBelongsToManyCaller();
        }

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related();

        if ($applyConnection) {
            $instance->setConnection($this->getConnection()->getName());
        }

        $otherKey = $otherKey ?: $instance->getForeignKey();

        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        $query = $instance->newQuery();

        return new BelongsToMany($query, $this, $table, $foreignKey, $otherKey, $relation);
    }

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

    protected function setRelationConnection($model)
    {
        if ($model instanceof Eloquent) {
            $model->setConnection($this->getConnectionName());
        }
    }
}
