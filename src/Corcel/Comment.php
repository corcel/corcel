<?php 

namespace Corcel;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Comment extends Eloquent
{
    protected $table = 'wp_comments';
    protected $primaryKey = 'comment_ID';

    public function post()
    {
        return $this->belongsTo('Corcel\Post', 'comment_post_ID');
    }

    public function original()
    {
        return $this->belongsTo('Corcel\Comment', 'comment_parent');
    }

    public function replies()
    {
        return $this->hasMany('Corcel\Comment', 'comment_parent');
    }

    public function isApproved()
    {
        return $this->attributes['comment_approved'] == 1;
    }

    public function isReply()
    {
        return $this->attributes['comment_parent'] > 0;
    }

    public function hasReplies()
    {
        return count($this->replies) > 0;
    }

    public static function findByPostId($postId)
    {
        $instance = new static;
        return $instance->where('comment_post_ID', $postId)->get();
    }

    public function newQuery($excludeDeleted = true)
    {
        $builder = new CommentBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);

        if ($excludeDeleted and $this->softDelete) {
            $builder->whereNull($this->getQualifiedDeletedAtColumn());
        }

        return $builder;
    }
}
