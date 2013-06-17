<?php 

namespace Corcel;

use Illuminate\Database\Eloquent\Builder;

class PostBuilder extends Builder
{
    public function status($postStatus)
    {
        return $this->where('post_status', $postStatus);
    }

    public function published()
    {
        return $this->status('publish');
    }

    public function type($type)
    {
        return $this->where('post_type', $type);
    }
}