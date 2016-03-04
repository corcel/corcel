<?php

/**
 * Corcel\Comment
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */

namespace Corcel;

class Comment extends Model
{
    const CREATED_AT = 'comment_date';
    const UPDATED_AT = null;

    protected $table = 'comments';
    protected $primaryKey = 'comment_ID';
    protected $dates = ['comment_date'];

    /**
     * Post relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo('Corcel\Post', 'comment_post_ID');
    }

    /**
     * Original relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function original()
    {
        return $this->belongsTo('Corcel\Comment', 'comment_parent');
    }

    /**
     * Replies relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany('Corcel\Comment', 'comment_parent');
    }

    /**
     * Verify if the current comment is approved
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->attributes['comment_approved'] == 1;
    }

    /**
     * Verify if the current comment is a reply from another comment
     *
     * @return bool
     */
    public function isReply()
    {
        return $this->attributes['comment_parent'] > 0;
    }

    /**
     * Verify if the current comment has replies
     *
     * @return bool
     */
    public function hasReplies()
    {
        return count($this->replies) > 0;
    }

    /**
     * Find a comment by post ID
     *
     * @param int $postId
     * @return \Corcel\Comment
     */
    public static function findByPostId($postId)
    {
        $instance = new static;

        return $instance->where('comment_post_ID', $postId)->get();
    }

    /**
     * Override the parent newQuery() to the custom CommentBuilder class
     *
     * @param bool $excludeDeleted
     * @return \Corcel\CommentBuilder
     */
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
