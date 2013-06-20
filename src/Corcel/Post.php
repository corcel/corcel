<?php 

namespace Corcel;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Post extends Eloquent
{
    protected $table = 'wp_posts';
    protected $primaryKey = 'ID';
    protected $with = array('meta', 'comments');

    public function meta()
    {
        return $this->hasMany('Corcel\PostMeta', 'post_id');
    }

    public function comments()
    {
        return $this->hasMany('Corcel\Comment', 'comment_post_ID');
    }

    public function newQuery($excludeDeleted = true)
    {
        $builder = new PostBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);
        // $builder->published();

        if (isset($this->postType) and $this->postType) {
            $builder->type($this->postType);
        }

        if ($excludeDeleted and $this->softDelete) {
            $builder->whereNull($this->getQualifiedDeletedAtColumn());
        }

        return $builder;
    }

    public function __get($key)
    {
        if (!isset($this->$key)) {
            return $this->meta->$key;    
        }

        return parent::__get($key);
    }

}