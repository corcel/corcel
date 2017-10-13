<?php

namespace Corcel\Laravel\Resource;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class Post
 *
 * @package Corcel\Resource
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Post extends Resource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->ID,
            'title' => $this->resource->post_title,
        ];
    }
}
