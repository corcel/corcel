<?php 

namespace Corcel;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Post extends Eloquent
{
    protected $table = 'wp_posts';
    protected $primaryKey = 'ID';
    protected $with = array('meta');

    public function meta()
    {
        return $this->hasMany('Corcel\PostMeta', 'post_id');
    }

    public function newQuery($excludeDeleted = true)
    {
        $builder = new PostBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);
        $builder->published();

        if (isset($this->postType)) {
            $builder->type($this->postType);
        }

        if ($excludeDeleted and $this->softDelete) {
            $builder->whereNull($this->getQualifiedDeletedAtColumn());
        }

        return $builder;
    }

}