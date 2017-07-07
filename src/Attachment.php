<?php

namespace Corcel;

use Corcel\Traits\AliasesTrait;

/**
 * Class Attachment
 *
 * @package Corcel
 * @author José CI <josec89@gmail.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Attachment extends Post
{
    use AliasesTrait;

    /**
     * Type of post.
     *
     * @var string
     */
    protected $postType = 'attachment';

    /**
     * @var array
     */
    protected $appends = [
        'title',
        'url',
        'type',
        'description',
        'caption',
        'alt',
    ];

    /**
     * @var array
     */
    protected static $aliases = [
        'title' => 'post_title',
        'url' => 'guid',
        'type' => 'post_mime_type',
        'description' => 'post_content',
        'caption' => 'post_excerpt',
        'alt' => ['meta' => '_wp_attachment_image_alt'],
    ];

    /**
     * @return array
     */
    public function toArray()
    {
        return collect($this->appends)->map(function ($field) {
            return [$field => $this->getAttribute($field)];
        })->collapse()->toArray();
    }
}
