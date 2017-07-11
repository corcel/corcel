<?php

namespace Corcel\Model\Meta;

use Corcel\Model;
use Corcel\Model\Collection\UserMetaCollection;

/**
 * Class UserMeta
 *
 * @package Corcel\Model\Meta
 * @author Mickael Burguet <www.rundef.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class UserMeta extends Model
{
    /**
     * @var string
     */
    protected $table = 'usermeta';

    /**
     * @var string
     */
    protected $primaryKey = 'umeta_id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['meta_key', 'meta_value', 'user_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Corcel\User');
    }

    /**
     * @param array $models
     * @return UserMetaCollection|\Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new UserMetaCollection($models);
    }
}
