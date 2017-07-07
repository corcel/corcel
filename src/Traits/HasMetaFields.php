<?php

namespace Corcel\Traits;

use Corcel\Attachment;
use Corcel\CustomLink;
use Corcel\MenuItem;
use Corcel\Post;
use Corcel\PostMeta;
use Illuminate\Database\Eloquent\Builder;
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
     * @var array
     */
    private $relatedMetaClasses = [
        Attachment::class => Post::class,
        CustomLink::class => Post::class,
        MenuItem::class => Post::class,
    ];

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
     * @param Builder $query
     * @param string $meta
     * @param mixed $value
     * @return Builder
     */
    public function scopeHasMeta(Builder $query, $meta, $value = null)
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
     * @todo Add support to array in $key
     */
    public function saveMeta($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->saveOneMeta($k, $v);
            }

            $this->load('meta');

            return true;
        }

        return $this->saveOneMeta($key, $value);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    private function saveOneMeta($key, $value)
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
     * @param string $key
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection
     */
    public function createMeta($key, $value = null)
    {
        if (is_array($key)) {
            return collect($key)->map(function ($value, $key) {
                return $this->createOneMeta($key, $value);
            });
        }

        return $this->createOneMeta($key, $value);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function createOneMeta($key, $value)
    {
        return $this->meta()->create([
            'meta_key' => $key,
            'meta_value' => $value,
        ]);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createField($key, $value)
    {
        return $this->createMeta($key, $value);
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

        return sprintf('%s_id', strtolower($callerName));
    }

    /**
     * @return string
     */
    private function getCallerClassName()
    {
        $class = static::class;

        if ($relation = Arr::get($this->relatedMetaClasses, $class)) {
            $class = $relation;
        }

        return Arr::last(explode('\\', $class));
    }
}
