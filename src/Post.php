<?php

namespace Corcel;

use Corcel\Traits\AliasesTrait;
use Corcel\Traits\CreatedAtTrait;
use Corcel\Traits\HasAcfFields;
use Corcel\Traits\HasMetaFields;
use Corcel\Traits\ShortcodesTrait;
use Corcel\Traits\UpdatedAtTrait;

/**
 * Class Post
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Post extends Model
{
    use AliasesTrait;
    use CreatedAtTrait;
    use HasAcfFields;
    use HasMetaFields;
    use ShortcodesTrait;
    use UpdatedAtTrait;

    const CREATED_AT = 'post_date';
    const UPDATED_AT = 'post_modified';

    /** @var array */
    protected static $postTypes = [];

    protected $table = 'posts';
    protected $primaryKey = 'ID';
    protected $dates = ['post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt'];
    protected $with = ['meta'];

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
     * The accessors to append to the model's array form.
     *
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

        // Terms inside all taxonomies
        'terms',

        // Terms analysis
        'main_category',
        'keywords',
        'keywords_str',
    ];

    /**
     * @var array
     */
    protected $aliases = [
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
     */
    public function __construct(array $attributes = [])
    {
        foreach ($this->fillable as $field) {
            if (!isset($attributes[$field])) {
                $attributes[$field] = '';
            }
        }

        parent::__construct($attributes);
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
            TermTaxonomy::class, 'term_relationships', 'object_id', 'term_taxonomy_id'
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
     * @param bool $excludeDeleted
     * @return PostBuilder
     */
    public function newQuery($excludeDeleted = true)
    {
        $builder = new PostBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);
        // disabled the default orderBy because else Post::all()->orderBy(..)
        // is not working properly anymore.
        // $builder->orderBy('post_date', 'desc');

        if (isset($this->postType) and $this->postType) {
            $builder->type($this->postType);
        }

        if ($excludeDeleted and $this->softDelete) {
            $builder->whereNull($this->getQualifiedDeletedAtColumn());
        }

        return $builder;
    }

    /**
     * Magic method to return the meta data like the post original fields.
     *
     * @param string $key
     *
     * @return string
     * @todo This is a mess. Refactor this
     */
    public function __get($key)
    {
        if (($value = parent::__get($key)) !== null) {
            return $value;
        }

        if (!property_exists($this, $key)) {
            if (property_exists($this, $this->primaryKey) && isset($this->meta->$key)) {
                return $this->meta->$key;
            }
        } elseif (isset($this->$key) and empty($this->$key)) {
            // fix for menu items when chosing category to show
            if (in_array($key, ['post_title', 'post_name'])) {
                $type = $this->meta->_menu_item_object;
                $taxonomy = null;

                // Support certain types of meta objects
                if ($type == 'category') {
                    $taxonomy = $this->meta()->where('meta_key', '_menu_item_object_id')
                        ->first()->taxonomy('meta_value')->first();
                } elseif ($type == 'post_tag') {
                    $taxonomy = $this->meta()->where('meta_key', '_menu_item_object_id')
                        ->first()->taxonomy('meta_value')->first();
                } elseif ($type == 'post') {
                    $post = $this->meta()->where('meta_key', '_menu_item_object_id')
                        ->first()->post(true)->first();

                    return $post->$key;
                }

                if (isset($taxonomy) && $taxonomy->exists) {
                    if ($key == 'post_title') {
                        return $taxonomy->name;
                    } elseif ($key == 'post_name') {
                        return $taxonomy->slug;
                    }
                }
            }
        }
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        if (isset($this->attributes[$this->primaryKey])) {
            $this->meta->save($this->attributes[$this->primaryKey]);
        }

        return parent::save($options);
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
        $taxonomies = $this->taxonomies;
        $terms = [];

        foreach ($taxonomies as $taxonomy) {
            $taxonomyName = $taxonomy['taxonomy'] == 'post_tag' ? 'tag' : $taxonomy['taxonomy'];
            $terms[$taxonomyName][$taxonomy->term['slug']] = $taxonomy->term['name'];
        }

        return $terms;
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
        $keywords = [];

        if ($this->terms) {
            foreach ($this->terms as $taxonomy) {
                foreach ($taxonomy as $term) {
                    $keywords[] = $term;
                }
            }
        }

        return $keywords;
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
     * Overrides default behaviour by instantiating class based on the $attributes->post_type value.
     *
     * By default, this method will always return an instance of the calling class. However if post types have
     * been registered with the Post class using the registerPostType() static method, this will now return an
     * instance of that class instead.
     *
     * If the post type string from $attributes->post_type does not appear in the static $postTypes array,
     * then the class instantiated will be the called class (the default behaviour of this method).
     *
     * @param array $attributes
     * @param null  $connection
     *
     * @return mixed
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        if (is_object($attributes) && array_key_exists($attributes->post_type, static::$postTypes)) {
            $class = static::$postTypes[$attributes->post_type];
        } elseif (is_array($attributes) && array_key_exists($attributes['post_type'], static::$postTypes)) {
            $class = static::$postTypes[$attributes['post_type']];
        } else {
            $class = get_called_class();
        }

        $model = new $class([]);
        $model->exists = true;

        $model->setRawAttributes((array) $attributes, true);
        $model->setConnection($connection ?: $this->connection);

        return $model;
    }

    /**
     * Register your Post Type classes here to have them be instantiated instead of the standard Post model.
     *
     * This method allows you to register classes that will be used for specific post types as defined in the post_type
     * column of the wp_posts table. If a post type is registered here, when a Post object is returned from the posts
     * table it will be automatically converted into the appropriate class for its post type.
     *
     * If you register a Page class for the post_type 'page', then whenever a Post is fetched from the database that has
     * its post_type has 'page', it will be returned as a Page instance, instead of the default and generic
     * Post instance.
     *
     * @param string $name  The name of the post type (e.g. 'post', 'page', 'custom_post_type')
     * @param string $class The class that represents the post type model (e.g. 'Post', 'Page', 'CustomPostType')
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
}
