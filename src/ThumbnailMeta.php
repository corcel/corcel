<?php

namespace Corcel;

class ThumbnailMeta extends PostMeta
{
    const SIZE_THUMBNAIL = 'thumbnail';
    const SIZE_MEDIUM    = 'medium';
    const SIZE_LARGE     = 'large';
    const SIZE_FULL      = 'full';

    protected $with = ['attachment'];

    public function attachment()
    {
        return $this->belongsTo('Corcel\Attachment', 'meta_value');
    }

    public function __toString()
    {
        return $this->attachment->guid;
    }

    public function size($size)
    {
        if ($size  == self::SIZE_FULL) {
            return $this->attachment->url;
        }

        $sizes = $this->attachment->meta->_wp_attachment_metadata['sizes'];

        if (! isset($sizes[$size])) {
            throw new \Exception('Invalid size: ' . $size);
        }

        return dirname($this->attachment->url)
            . '/'
            . $sizes[$size]['file'];
    }
}
