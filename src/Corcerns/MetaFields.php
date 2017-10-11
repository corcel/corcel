<?php

namespace Corcel\Corcerns;

use Corcel\Model\Meta\PostMeta;
use Corcel\Model\Post;
use Illuminate\Database\Eloquent\Builder;
use ReflectionClass;

/**
 * Trait HasMetaFields
 *
 * @package Corcel\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait MetaFields
{
    /**
     * @var array
     */
    private $customMetaClasses = [
        \Corcel\Model\Comment::class,
        \Corcel\Model\Term::class,
        \Corcel\Model\User::class,
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
        if (!is_array($meta)) {
            $meta = [$meta => $value];
        }

        foreach ($meta as $key => $value) {
            $query->whereHas('meta', function ($query) use ($key, $value) {
                if (is_string($key)) {
                    $query->where('meta_key', $key);

                    return is_null($value) ? $query : // 'foo' => null
                        $query->where('meta_value', $value); // 'foo' => 'bar'
                }

                return $query->where('meta_key', $value); // 0 => 'foo'
            });
        }

        return $query;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
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

        $result = $meta->fill(['meta_value' => $value])->save();

        $this->load('meta');

        return $result;
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
        $meta =  $this->meta()->create([
            'meta_key' => $key,
            'meta_value' => $value,
        ]);

        $this->load('meta');

        return $meta;
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
     * @param string $attribute
     * @return mixed|null
     */
    public function getMeta($attribute)
    {
        if ($meta = $this->meta->{$attribute}) {
            return $meta;
        }

        return null;
    }

    /**
     * @return string
     */
    private function getClassName()
    {
        $className = sprintf(
            'Corcel\\Model\\Meta\\%sMeta', $this->getCallerClassName()
        );

        return class_exists($className) ?
            $className :
            PostMeta::class;
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

        if (!in_array($class, $this->customMetaClasses)) {
            $class = Post::class;
        }

        return (new ReflectionClass($class))->getShortName();
    }
}
