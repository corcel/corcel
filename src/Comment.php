<?php

namespace Corcel;

use Corcel\Traits\HasMetaFields;
use Corcel\Traits\TimestampsTrait;

/**
 * Class Comment
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Comment extends Model
{
    use HasMetaFields;
    use TimestampsTrait;

    const CREATED_AT = 'comment_date';
    const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'comments';

    /**
     * @var string
     */
    protected $primaryKey = 'comment_ID';

    /**
     * @var array
     */
    protected $dates = ['comment_date'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'comment_post_ID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function original()
    {
        return $this->belongsTo(Comment::class, 'comment_parent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->original();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'comment_parent');
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->attributes['comment_approved'] == 1;
    }

    /**
     * @return bool
     */
    public function isReply()
    {
        return $this->attributes['comment_parent'] > 0;
    }

    /**
     * @return bool
     */
    public function hasReplies()
    {
        return $this->replies->count() > 0;
    }

    /**
     * Find a comment by post ID.
     *
     * @param int $postId
     * @return \Corcel\Comment
     */
    public static function findByPostId($postId)
    {
        $instance = new static();

        return $instance->where('comment_post_ID', $postId)->get();
    }

    /**
     * Override the parent newQuery() to the custom CommentBuilder class.
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
