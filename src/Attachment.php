<?php

namespace Corcel;

/**
 * Attachment model
 * Attachments are only a special type of posts.
 *
 * @author José CI <josec89@gmail.com>
 */
class Attachment extends Post
{
    /**
     * Type of post.
     *
     * @var string
     */
    protected $postType = 'attachment';

    /**
     * The accessors to append to the model's array form.
     *
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
    protected $aliases = [
        'title' => 'post_title',
        'url' => 'guid',
        'type' => 'post_mime_type',
        'description' => 'post_content',
        'caption' => 'post_excerpt',
        'alt' => ['meta' => '_wp_attachment_image_alt'],
    ];

    /**
     * Returns the basic Attachment information.
     *
     * @return string
     * @todo Refactor to collection
     */
    public function toArray()
    {
        foreach ($this->appends as $field) {
            $result[$field] = $this[$field];
        }

        return $result;
    }
}
