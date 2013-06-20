<?php 

namespace Corcel;

use Illuminate\Database\Eloquent\Builder;

class CommentBuilder extends Builder
{
    public function approved()
    {
        return $this->where('comment_approved', 1);
    }

}