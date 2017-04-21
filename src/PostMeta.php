<?php

/**
 * Corcel\PostMeta.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */

namespace Corcel;

use Exception;

class PostMeta extends Model
{
    protected $table = 'postmeta';
    protected $primaryKey = 'meta_id';
    public $timestamps = false;
    protected $fillable = ['meta_key', 'meta_value', 'post_id'];

    /**
     * Post relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post($ref = false)
    {
        if ($ref) {
            $this->primaryKey = 'meta_value';

            return $this->hasOne('Corcel\Post', 'ID');
        }

        return $this->belongsTo('Corcel\Post');
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['value'];

    /**
     * Gets the value.
     * Tries to unserialize the object and returns the value if that doesn't work.
     *
     * @return value
     */
    public function getValueAttribute()
    {
        if ($this->is_serialized($this->meta_value) === TRUE) {
            try {
                $value = unserialize($this->meta_value);
                // if we get false, but the original value is not false then something has gone wrong.
                // return the meta_value as is instead of unserializing
                // added this to handle cases where unserialize doesn't throw an error that is catchable
                return $value === false && $this->meta_value !== false ? $this->meta_value : $value;
            } catch (Exception $ex) {
                return $this->meta_value;
            }
        }

        return $this->meta_value;
    }

    /**
     * Taxonomy relationship from the meta_value.
     *
     * @param string $key
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function taxonomy($primary = null, $where = null)
    {
        // possible to exclude a relationship connection
        if (!is_null($primary) && !empty($primary)) {
            $this->primaryKey = $primary;
        }

        // load relationship
        $relation = $this->hasOne('Corcel\TermTaxonomy', 'term_taxonomy_id');

        // do we need to filter which value to look for with meta_value
        // if (!is_null($where) && !empty($where)) {
        //     $relation->where($where, $this->meta_value);
        // }

        return $relation;
    }

    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \Corcel\PostMetaCollection
     */
    public function newCollection(array $models = [])
    {
        return new PostMetaCollection($models);
    }
    
    /**
     * Check whether string is serialized data
     *
     * @since 2.0.5
     *
     * @param string $data Serialized data
     * @return bool False if not a serialized string, true if it is.
     */
    private function is_serialized($data)
    {
        // if it isn't a string, it isn't serialized
        if (!is_string($data)) {
            return false;
        }

        $data = trim($data);

        if ($data == 'N;') {
            return true;
        }

        if (!preg_match( '/^([adObis]):/', $data, $badions)) {
            return false;
        }

        switch ($badions[1]) {
            case 'a' :
            case 'O' :
            case 's' :
                if (preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
                {
                    return true;
                }
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if (preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data))
                {
                    return true;
                }
                break;
        }

        return false;
    }
}
