<?php 

/**
 * Corcel\CommentBuilder
 * 
 * @author Junior Grossi <me@juniorgrossi.com>
 */

namespace Corcel;

use Illuminate\Database\Eloquent\Builder;

class CommentBuilder extends Builder
{
    /**
     * Where clause for only approved comments
     * 
     * @return \Corcel\CommentBuilder
     */
    public function approved()
    {
        return $this->where('comment_approved', 1);
    }

}