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
            'type' => $this->resource->post_type,
            'title' => $this->resource->post_title,
            'slug' => $this->resource->post_name,
            'excerpt' => $this->resource->post_excerpt,
            'content' => $this->resource->post_content,
            'status' => $this->resource->post_status,
            'url' => $this->resource->guid,
            'comment_status' => $this->resource->comment_status,
            'ping_status' => $this->resource->ping_status,
            'password' => $this->resource->post_password,
            'created_at' => $this->resource->post_date,
            'updated_at' => $this->resource->post_modified,
        ];
    }
}
