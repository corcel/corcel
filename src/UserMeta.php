<?php

/**
 * Corcel\UserMeta
 *
 * @author Mickael Burguet <www.rundef.com>
 */

namespace Corcel;

use Illuminate\Database\Eloquent\Model as Eloquent;

class UserMeta extends Eloquent
{
    protected $table = 'usermeta';
    protected $primaryKey = 'umeta_id';
    public $timestamps = false;
    protected $fillable = array('meta_key', 'meta_value', 'user_id');

    /**
     * User relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Corcel\User');
    }

    /**
     * Override newCollection() to return a custom collection
     *
     * @param array $models
     * @return \Corcel\UserMetaCollection
     */
    public function newCollection(array $models = array())
    {
        return new UserMetaCollection($models);
    }
}