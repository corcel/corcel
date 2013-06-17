<?php 

namespace Corcel;

use Illuminate\Database\Eloquent\Model as Eloquent;

class PostMeta extends Eloquent
{
    protected $table = 'wp_postmeta';
    protected $primaryKey = 'meta_id';

    public function post()
    {
        return $this->belongsTo('Post');
    }

    public function newCollection(array $models = array())
    {
        return new PostMetaCollection($models);
    }
}