<?php

/**
 * Base model.
 *
 * @author Mickael Burguet <www.rundef.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
namespace Corcel;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Model extends Eloquent
{
    /**
     * Replace the original hasMany function to forward the connection name
     *
     * @param string $related
     * @param null $foreignKey
     * @param null $localKey
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related();
        $instance->setConnection($this->getConnection()->getName());

        $localKey = $localKey ?: $this->getKeyName();

        return new HasMany($instance->newQuery(), $this, $foreignKey, $localKey);
    }

    /**
     * Replace the original hasOne function to forward the connection name
     *
     * @param string $related
     * @param null $foreignKey
     * @param null $localKey
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related;
        $instance->setConnection($this->getConnection()->getName());

        $localKey = $localKey ?: $this->getKeyName();

        return new HasOne($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }

    /**
     * Replace the original belongsTo function to forward the connection name
     *
     * @param string $related
     * @param null $foreignKey
     * @param null $otherKey
     * @param null $relation
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
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
        $instance->setConnection($this->getConnection()->getName());

        $query = $instance->newQuery();

        $otherKey = $otherKey ?: $instance->getKeyName();

        return new BelongsTo($query, $this, $foreignKey, $otherKey, $relation);
    }

    /**
     * Replace the original belongsToMany function to forward the connection name
     *
     * @param string $related
     * @param null $table
     * @param null $foreignKey
     * @param null $otherKey
     * @param null $relation
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
    {
        if (is_null($relation)) {
            $relation = $this->getBelongsToManyCaller();
        }

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related();
        $instance->setConnection($this->getConnection()->getName());

        $otherKey = $otherKey ?: $instance->getForeignKey();

        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        $query = $instance->newQuery();

        return new BelongsToMany($query, $this, $table, $foreignKey, $otherKey, $relation);
    }

    /**
     * Get the relation value setting the connection name
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
     * Set the connection name to model
     *
     * @param $model
     */
    protected function setRelationConnection($model)
    {
        if ($model instanceof Eloquent) {
            $model->setConnection($this->getConnectionName());
        }
    }
}
