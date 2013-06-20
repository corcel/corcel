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

    public static function findBySlug($slug)
    {
        $instance = new static;
        $builder = $instance->newQuery();
        $post = $builder->where('post_name', $slug)->first();

        if ($post == null) {
            throw new \Exception("Post not found with slug [{$slug}] and postType [{$instance->postType}]");
        }

        return $post;
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