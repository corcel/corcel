<?php

namespace Corcel\Model\Meta;

use Corcel\Model\Comment;

/**
 * Class CommentMeta
 *
 * @package Corcel\Model\Meta
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class CommentMeta extends PostMeta
{
    /**
     * @var string
     */
    protected $table = 'commentmeta';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}
