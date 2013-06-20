<?php 

namespace Corcel;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Comment extends Eloquent
{
    protected $table = 'wp_comments';
    protected $primaryKey = 'comment_ID';
    protected $with = array('post', 'original', 'replies');

    public function post()
    {
        return $this->belongsTo('Corcel\Post');
    }

    public function original()
    {
        return $this->belongsTo('Corcel\Comment');
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

}