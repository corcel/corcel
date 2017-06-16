<?php

namespace Corcel\Traits;

use Corcel\Post;
use Corcel\PostMeta;
use Corcel\TermMeta;
use Illuminate\Support\Arr;

/**
 * Trait HasMetaFields
 *
 * @package Corcel\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait HasMetaFields
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany(
            $this->getClassName(), $this->getFieldName()
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields()
    {
        return $this->meta();
    }

    /**
     * Meta filter scope.
     *
     * @param $query
     * @param $meta
     * @param null $value
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function scopeHasMeta($query, $meta, $value = null)
    {
        return $query->whereHas('meta', function ($query) use ($meta, $value) {
            $query->where('meta_key', $meta);
            if (!is_null($value)) {
                $query->where('meta_value', $value);
            }
        });
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function saveMeta($key, $value)
    {
        $meta = $this->meta()->where('meta_key', $key)
            ->firstOrNew(['meta_key' => $key]);

        return $meta->fill(['meta_value' => $value])->save();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function saveField($key, $value)
    {
        return $this->saveMeta($key, $value);
    }

    /**
     * @return string
     */
    private function getClassName()
    {
        $className = sprintf(
            'Corcel\\%sMeta', $this->getCallerClassName()
        );

        return class_exists($className) ? $className : PostMeta::class;
    }

    /**
     * @return string
     */
    private function getFieldName()
    {
        $callerName = $this->getCallerClassName();
        $className = $this->getClassName();

        return sprintf('%s_id', strtolower($callerName));
    }

    /**
     * @return string
     */
    private function getCallerClassName()
    {
        return Arr::last(explode('\\', static::class));
    }
}
