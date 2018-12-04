<?php

namespace Corcel\Model;

use Corcel\Concerns\AdvancedCustomFields;
use Corcel\Concerns\Aliases;
use Corcel\Concerns\CustomTimestamps;
use Corcel\Concerns\MetaFields;
use Corcel\Concerns\OrderScopes;
use Corcel\Concerns\Shortcodes;
use Corcel\Corcel;
use Corcel\Model;
use Corcel\Model\Builder\PostBuilder;
use Corcel\Model\Meta\ThumbnailMeta;

/**
 * Class Post
 *
 * @package Corcel\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 * @author Mickael Burguet <www.rundef.com>
 */
class Post extends Model
{
    use Aliases;
    use AdvancedCustomFields;
    use MetaFields;
    use Shortcodes;
    use OrderScopes;
    use CustomTimestamps;

    const CREATED_AT = 'post_date';
    const UPDATED_AT = 'post_modified';

    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * @var array
     */
    protected $dates = ['post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt'];

    /**
     * @var array
     */
    protected $with = ['meta'];

    /**
     * @var array
     */
    protected static $postTypes = [];

    /**
     * @var array
     */
    protected $fillable = [
        'post_content',
        'post_title',
        'post_excerpt',
        'post_type',
        'to_ping',
        'pinged',
        'post_content_filtered',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'title',
        'slug',
        'content',
        'type',
        'mime_type',
        'url',
        'author_id',
        'parent_id',
        'created_at',
        'updated_at',
        'excerpt',
        'status',
        'image',
        'terms',
        'main_category',
        'keywords',
        'keywords_str',
    ];

    /**
     * @var array
     */
    protected static $aliases = [
        'title' => 'post_title',
        'content' => 'post_content',
        'excerpt' => 'post_excerpt',
        'slug' => 'post_name',
        'type' => 'post_type',
        'mime_type' => 'post_mime_type',
        'url' => 'guid',
        'author_id' => 'post_author',
        'parent_id' => 'post_parent',
        'created_at' => 'post_date',
        'updated_at' => 'post_modified',
        'status' => 'post_status',
    ];

    /**
     * @param array $attributes
     * @param null $connection
     * @return mixed
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = $this->getPostInstance((array)$attributes);

        $model->exists = true;

        $model->setRawAttributes((array)$attributes, true);

        $model->setConnection(
            $connection ?: $this->getConnectionName()
        );

        return $model;
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function getPostInstance(array $attributes)
    {
        $class = static::class;

        // Check if it should be instantiated with a custom post type class
        if (isset($attributes['post_type']) && $attributes['post_type']) {
            if (isset(static::$postTypes[$attributes['post_type']])) {
                $class = static::$postTypes[$attributes['post_type']];
            } elseif (Corcel::isLaravel()) {
                $postTypes = config('corcel.post_types');
                if (is_array($postTypes) && isset($postTypes[$attributes['post_type']])) {
                    $class = $postTypes[$attributes['post_type']];
                }
            }
        }

        return new $class();
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return PostBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new PostBuilder($query);
    }

    /**
     * @return PostBuilder
     */
    public function newQuery()
    {
        return $this->postType ?
            parent::newQuery()->type($this->postType) :
            parent::newQuery();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function thumbnail()
    {
        return $this->hasOne(ThumbnailMeta::class, 'post_id')
            ->where('meta_key', '_thumbnail_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taxonomies()
    {
        return $this->belongsToMany(
            Taxonomy::class, 'term_relationships', 'object_id', 'term_taxonomy_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'comment_post_ID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'post_author');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Post::class, 'post_parent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Post::class, 'post_parent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachment()
    {
        return $this->hasMany(Post::class, 'post_parent')
            ->where('post_type', 'attachment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function revision()
    {
        return $this->hasMany(Post::class, 'post_parent')
            ->where('post_type', 'revision');
    }

    /**
     * Whether the post contains the term or not.
     *
     * @param string $taxonomy
     * @param string $term
     * @return bool
     */
    public function hasTerm($taxonomy, $term)
    {
        return isset($this->terms[$taxonomy]) &&
            isset($this->terms[$taxonomy][$term]);
    }

    /**
     * @return string
     */
    public function getPostType()
    {
        return $this->postType;
    }

    /**
     * @return string
     */
    public function getContentAttribute()
    {
        return $this->stripShortcodes($this->post_content);
    }

    /**
     * @return string
     */
    public function getExcerptAttribute()
    {
        return $this->stripShortcodes($this->post_excerpt);
    }

    /**
     * Gets the featured image if any
     * Looks in meta the _thumbnail_id field.
     *
     * @return string
     */
    public function getImageAttribute()
    {
        if ($this->thumbnail and $this->thumbnail->attachment) {
            return $this->thumbnail->attachment->guid;
        }
    }

    /**
     * Gets all the terms arranged taxonomy => terms[].
     *
     * @return array
     */
    public function getTermsAttribute()
    {
        return $this->taxonomies->groupBy(function ($taxonomy) {
            return $taxonomy->taxonomy == 'post_tag' ?
                'tag' : $taxonomy->taxonomy;
        })->map(function ($group) {
            return $group->mapWithKeys(function ($item) {
                return [$item->term->slug => $item->term->name];
            });
        })->toArray();
    }

    /**
     * Gets the first term of the first taxonomy found.
     *
     * @return string
     */
    public function getMainCategoryAttribute()
    {
        $mainCategory = 'Uncategorized';

        if (!empty($this->terms)) {
            $taxonomies = array_values($this->terms);

            if (!empty($taxonomies[0])) {
                $terms = array_values($taxonomies[0]);
                $mainCategory = $terms[0];
            }
        }

        return $mainCategory;
    }

    /**
     * Gets the keywords as array.
     *
     * @return array
     */
    public function getKeywordsAttribute()
    {
        return collect($this->terms)->map(function ($taxonomy) {
            return collect($taxonomy)->values();
        })->collapse()->toArray();
    }

    /**
     * Gets the keywords as string.
     *
     * @return string
     */
    public function getKeywordsStrAttribute()
    {
        return implode(',', (array) $this->keywords);
    }

    /**
     * @param string $name The post type slug
     * @param string $class The class to be instantiated
     */
    public static function registerPostType($name, $class)
    {
        static::$postTypes[$name] = $class;
    }

    /**
     * Clears any registered post types.
     */
    public static function clearRegisteredPostTypes()
    {
        static::$postTypes = [];
    }

    /**
     * Get the post format, like the WP get_post_format() function.
     *
     * @return bool|string
     */
    public function getFormat()
    {
        $taxonomy = $this->taxonomies()
            ->where('taxonomy', 'post_format')
            ->first();

        if ($taxonomy && $taxonomy->term) {
            return str_replace(
                'post-format-', '', $taxonomy->term->slug
            );
        }

        return false;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        $value = parent::__get($key);

        if ($value === null && !property_exists($this, $key)) {
            return $this->meta->$key;
        }

        return $value;
    }
}
