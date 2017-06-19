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
     * Gets the title attribute.
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        return $this->post_title;
    }

    /**
     * Gets the url attribute.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->guid;
    }

    /**
     * Gets the mime type attribute.
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return $this->post_mime_type;
    }

    /**
     * Gets the description attribute.
     *
     * @return string
     */
    public function getDescriptionAttribute()
    {
        return $this->post_content;
    }

    /**
     * Gets the caption attribute.
     *
     * @return string
     */
    public function getCaptionAttribute()
    {
        return $this->post_excerpt;
    }

    /**
     * Gets the alt attribute.
     *
     * @return string
     */
    public function getAltAttribute()
    {
        return $this->meta->_wp_attachment_image_alt;
    }

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
