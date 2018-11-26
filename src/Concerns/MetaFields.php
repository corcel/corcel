<?php

namespace Corcel\Concerns;

use Illuminate\Database\Eloquent\Builder;
use UnexpectedValueException;

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
    protected $builtInClasses = [
        \Corcel\Model\Comment::class => \Corcel\Model\Meta\CommentMeta::class,
        \Corcel\Model\Post::class => \Corcel\Model\Meta\PostMeta::class,
        \Corcel\Model\Term::class => \Corcel\Model\Meta\TermMeta::class,
        \Corcel\Model\User::class => \Corcel\Model\Meta\UserMeta::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields()
    {
        return $this->meta();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany($this->getMetaClass(), $this->getMetaForeignKey());
    }

    /**
     * @return string
     *
     * @throws \UnexpectedValueException
     */
    protected function getMetaClass()
    {
        foreach ($this->builtInClasses as $model => $meta) {
            if ($this instanceof $model) {
                return $meta;
            }
        }

        throw new UnexpectedValueException(sprintf(
            '%s must extends one of Corcel built-in models: Comment, Post, Term or User.',
            static::class
        ));
    }

    /**
     * @return string
     *
     * @throws \UnexpectedValueException
     */
    protected function getMetaForeignKey()
    {
        foreach ($this->builtInClasses as $model => $meta) {
            if ($this instanceof $model) {
                $basename = class_basename($model);

                return sprintf('%s_id', strtolower($basename));
            }
        }

        throw new UnexpectedValueException(sprintf(
            '%s must extends one of Corcel built-in models: Comment, Post, Term or User.',
            static::class
        ));
    }

    /**
     * @param Builder $query
     * @param string $meta
     * @param mixed $value
     * @param string $operator
     * @return Builder
     */
    public function scopeHasMeta(Builder $query, $meta, $value = null, string $operator = '=')
    {
        if (!is_array($meta)) {
            $meta = [$meta => $value];
        }

        foreach ($meta as $key => $value) {
            $query->whereHas('meta', function (Builder $query) use ($key, $value, $operator) {
                if (!is_string($key)) {
                    return $query->where('meta_key', $operator, $value);
                }
                $query->where('meta_key', $operator, $key);

                return is_null($value) ? $query :
                    $query->where('meta_value', $operator, $value);
            });
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $meta
     * @param mixed $value
     * @return Builder
     */
    public function scopeHasMetaLike(Builder $query, $meta, $value = null)
    {
        return $this->scopeHasMeta($query, $meta, $value, 'like');
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
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createField($key, $value)
    {
        return $this->createMeta($key, $value);
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
}
